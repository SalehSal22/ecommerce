## التقارير والاختبارات

تم وضع جميع ملفات التقارير والاختبارات داخل مجلد باسم:

`our-tests`

ويحتوي هذا المجلد على:
يحتوي هذا المجلد على 
```text
our-tests/
├── race-condition-test
│   ├── 2026-05-18-195159_hyprshot.png
│   ├── race-condition-lock-test-result.json
│   └── race-condition-test.js
└── stress-test
    ├── stress.js
    ├── stress-test-with-octane
    │   ├── 2026-05-18-203943_hyprshot.png
    │   └── stress-test-with-octane-result.json
    ├── stress-test-without-octane
    │   ├── 2026-05-18-195839_hyprshot.png
    │   └── stress-test-without-octane-result.json
    └── users.json
```

سكربتات الجافا سكربت التي يقوم k6 بتشغيلها لاجراء اختبارات على السرفر، كل اختبار مرفق معه ملف json يحتوي على نتيجة الاختبار باالاضافة الى سكرينشوت للنتائج من اجل السهولة، اختبار ال stress test قمنا باجراؤه مرة مع octane بحيث كان avg الوقت الذي يتطلبه ال http request حوالي 7 ثواني لان ال development server يشتغل على بروسس واحد بينما مع octaine انخفضت الى اقل من ثانية لانه يستعمل 4 بروسسات على كل كور من المعالج ويقوم بابقائها حية بالذاكرة ولا يقوم بعمل bootstrap لل application عند كل مرة
