import { check, sleep } from "k6";
import { SharedArray } from "k6/data";
import http from "k6/http";

const SINGLE_STOCK_PRODUCT_ID = 17; // Ensure this product only has 1 in stock!
const BASE_URL = "http://localhost:8001/api";
const JSON_HEADERS = {
    Accept: "application/json",
    "Content-Type": "application/json",
};

export const options = {
    scenarios: {
        race_condition_test: {
            executor: "shared-iterations",
            vus: 1, // Only 1 VU needed because it fires the batch internally
            iterations: 1, // Run once for a clean test
        },
    },
};

const users = new SharedArray("seeded_users", function () {
    return JSON.parse(open("./users.json"));
});

// --- 1. SETUP PHASE ---
export function setup() {
    const user1 = users[0];
    const user2 = users[1];

    // Log in both users sequentially before the test starts
    const loginRes1 = http.post(
        `${BASE_URL}/auth/login`,
        JSON.stringify({
            email: user1.email,
            password: user1.password,
        }),
        { headers: JSON_HEADERS },
    );

    const loginRes2 = http.post(
        `${BASE_URL}/auth/login`,
        JSON.stringify({
            email: user2.email,
            password: user2.password,
        }),
        { headers: JSON_HEADERS },
    );

    if (loginRes1.status !== 200 || loginRes2.status !== 200) {
        throw new Error("Setup failed: Could not log in users.");
    }

    return {
        token1: loginRes1.json("data.access_token"),
        token2: loginRes2.json("data.access_token"),
    };
}

// --- 2. EXECUTION PHASE ---
export default function (data) {
    const cartPayload = JSON.stringify({
        quantity: 1,
        product_id: SINGLE_STOCK_PRODUCT_ID,
    });

    // ==========================================
    // STEP 1: ADD TO CART (Preparation)
    // ==========================================
    const cartReq1 = {
        method: "POST",
        url: `${BASE_URL}/cart`,
        body: cartPayload,
        params: {
            headers: {
                ...JSON_HEADERS,
                Authorization: `Bearer ${data.token1}`,
            },
        },
    };

    const cartReq2 = {
        method: "POST",
        url: `${BASE_URL}/cart`,
        body: cartPayload,
        params: {
            headers: {
                ...JSON_HEADERS,
                Authorization: `Bearer ${data.token2}`,
            },
        },
    };

    // Fire both cart additions. In k6, http.batch is synchronous and blocks
    // execution until all requests in the array are finished (awating them).
    const cartResponses = http.batch([cartReq1, cartReq2]);

    const addedToCart = check(cartResponses, {
        "User 1 cart updated": (r) =>
            r[0].status === 200 || r[0].status === 201,
        "User 2 cart updated": (r) =>
            r[1].status === 200 || r[1].status === 201,
    });

    if (!addedToCart) {
        console.log(cartResponses[0].body);
        console.log(
            `Cart Update Failed: User 1 Status: ${cartResponses[0].status}, User 2 Status: ${cartResponses[1].status}`,
        );
    }

    // Small buffer to ensure database connection is ready for the race
    sleep(0.5);

    // ==========================================
    // STEP 2: CHECKOUT (The Race Condition Test)
    // ==========================================
    const orderReq1 = {
        method: "POST",
        url: `${BASE_URL}/orders`,
        body: null, // No body required based on previous setup
        params: {
            headers: {
                ...JSON_HEADERS,
                Authorization: `Bearer ${data.token1}`,
            },
        },
    };

    const orderReq2 = {
        method: "POST",
        url: `${BASE_URL}/orders`,
        body: null,
        params: {
            headers: {
                ...JSON_HEADERS,
                Authorization: `Bearer ${data.token2}`,
            },
        },
    };

    // BOOM. Both checkout requests hit the server at the exact same millisecond.
    const orderResponses = http.batch([orderReq1, orderReq2]);

    console.log(`User 1 Checkout Status: ${orderResponses[0].status}`);
    console.log(`User 2 Checkout Status: ${orderResponses[1].status}`);

    // If your DB pessimistic/optimistic locks are working perfectly,
    // exactly one will return 201 (Created/Success), and the other will
    // return a 4xx (Out of Stock) or 500 (Deadlock/Exception).
    check(orderResponses, {
        "Lock held: One succeeded, One failed": (r) => {
            const statuses = [r[0].status, r[1].status];
            const oneSucceeded =
                statuses.includes(201) || statuses.includes(200);
            const oneFailed = statuses.some((s) => s >= 400);
            return oneSucceeded && oneFailed;
        },
    });
}
