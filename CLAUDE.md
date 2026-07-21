\# CLAUDE.md



Цей файл — інструкція для Claude Code при роботі з репозиторієм \*\*OpenFields for WordPress\*\*. Дотримуйся цих правил у кожній сесії, навіть якщо конкретна задача їх явно не згадує.



\## Про проєкт



OpenFields — Open Source (GPL v2+) плагін WordPress, вільна альтернатива ACF PRO: кастомні поля, групи полів, репітери, гнучкий контент, Gutenberg-блоки, REST API, GraphQL. Повне ТЗ — у `docs/TOR.md`. Завжди звіряйся з ним перед реалізацією нової фічі — не вигадуй поведінку, якої там немає, і питай, якщо щось не описано.



\## Стек і версії



\- \*\*PHP\*\*: 8.1+ (мінімальна сумісність — 7.4, але новий код пишемо під 8.1, без фіч, яких немає у 7.4, якщо це core-функціонал, а не адмінка).

\- \*\*WordPress\*\*: 6.4+.

\- \*\*TypeScript\*\*: 5.x, strict mode. Увесь новий JS-код — тільки `.ts`/`.tsx`. Ніякого нового `.js`/`.jsx`.

\- \*\*React\*\*: через `@wordpress/element`, компоненти — через `@wordpress/components`.

\- \*\*Збірка\*\*: `@wordpress/scripts` (webpack під капотом), не додавай власний webpack-конфіг без крайньої потреби.

\- \*\*Тести\*\*: PHPUnit (PHP), Playwright (E2E), `tsc --noEmit` (типи).



\## Команди



```bash

\# Встановлення залежностей

composer install

npm install



\# Розробка (watch-режим)

npm run start



\# Продакшн-збірка

npm run build



\# Перевірка типів TypeScript (без емісії файлів)

npm run type-check



\# Лінтинг

npm run lint:js        # ESLint + @wordpress/eslint-plugin

composer run lint:php  # PHPCS з WordPress Coding Standards



\# Тести

composer run test:unit        # PHPUnit

npm run test:e2e              # Playwright

npm run test:e2e -- --grep "repeater"   # окремий сценарій



\# Локальне середовище WP

npx wp-env start

npx wp-env stop

```



Перед тим як вважати задачу завершеною, ЗАВЖДИ запускай: `npm run type-check`, `npm run lint:js`, `composer run lint:php`, і релевантні тести. Не показуй код як готовий, якщо ці команди не пройшли.



\## Структура репозиторію



```

openfields/

├── openfields.php              # bootstrap-файл плагіна, тут НІЧОГО крім реєстрації/hooks

├── includes/

│   ├── Core/                   # автозавантаження, реєстрація CPT "openfields-group"

│   ├── FieldTypes/             # PHP-клас на кожен тип поля (валідація, сереалізація, збереження)

│   ├── FieldGroups/            # Location Rules, порядок полів, conditional logic (server-side)

│   ├── Admin/                  # PHP-контролери адмін-екранів (рендерять React root)

│   ├── Api/

│   │   ├── Rest/                # REST-контролери, permission\_callback ОБОВ'ЯЗКОВИЙ

│   │   └── GraphQL/             # резолвери для WPGraphQL (опційна залежність — перевіряй наявність класу)

│   ├── Blocks/                  # реєстрація Gutenberg-блоків (block.json + render callback)

│   ├── ImportExport/            # JSON export/import, local JSON sync

│   └── Support/                 # санітизація, кешування, хелпери — без побічних ефектів

├── assets/

│   ├── src/

│   │   ├── field-types/         # React-компоненти типів полів + Field Type API (TS)

│   │   ├── admin/                # конструктор груп полів, налаштування

│   │   └── blocks/                # edit/save для Gutenberg-блоків

│   └── build/                   # ЗГЕНЕРОВАНО, ніколи не редагувати руками

├── languages/                   # .pot/.po/.mo — не редагувати вручну, генерується скриптом

├── tests/

│   ├── unit/

│   ├── integration/

│   └── e2e/

└── docs/

&#x20;   ├── TOR.md                   # повне технічне завдання — джерело правди щодо скоупу

&#x20;   ├── hooks-reference.md

&#x20;   └── field-types-api.md

```



\## Правила іменування



\- PHP: namespace `OpenFields\\...`, PSR-4, класи `PascalCase`, файли `class-{name}.php` або `{Name}.php` відповідно до Composer autoload map — дивись `composer.json` перед створенням нового файлу.

\- Публічні PHP-функції (не класи) — префікс `openfields\_` (напр. `openfields\_add\_local\_field\_group()`, `openfields\_register\_field\_type()`). Ніколи не використовуй голі назви без префіксу — конфлікти неймспейсів у глобальному PHP критичні.

\- Хуки (actions/filters) — префікс `openfields/` (напр. `openfields/field/before\_save`, `openfields/field\_group/location\_rules`). Документуй кожен новий хук у `docs/hooks-reference.md` у тому ж PR.

\- Text domain для перекладів — завжди `openfields`, без винятків.

\- TS: типи/інтерфейси — `PascalCase` (`FieldConfig`, `FieldTypeDefinition`), файли компонентів — `PascalCase.tsx`, хуки — `useCamelCase.ts`.



\## Правила для PHP-коду



\- Ніяких прямих SQL без `$wpdb->prepare()`.

\- Санітизація вхідних даних (`sanitize\_text\_field`, `absint`, тощо) і екранування вихідних (`esc\_html`, `esc\_attr`, `esc\_url`) — обов'язково, навіть якщо "джерело довірене".

\- Кожна форма збереження — nonce-перевірка (`check\_admin\_referer` / `wp\_verify\_nonce`).

\- Кожен REST-ендпоінт — обов'язковий `permission\_callback`, ніколи `\_\_return\_true` без явного коментаря чому це безпечно.

\- Не використовуй `extract()`, `eval()`, динамічні виклики функцій із неперевіреного вводу.

\- Сумісність зі старим PHP (де застосовно) — не використовуй `readonly` властивості, enums, чи інші фічі 8.1+ у коді, що має підтримувати 7.4, без явного узгодження.

\- Дотримуйся WordPress Coding Standards (PHPCS) — не переписуй правила локально без причини.



\## Правила для TypeScript/React



\- `strict: true` в tsconfig — не вимикай і не обходь через `any` без крайньої потреби. Якщо `any` неминучий — коментар чому.

\- Кожен новий тип поля реалізує інтерфейс `FieldTypeDefinition` з `assets/src/field-types/types.ts` (див. `docs/field-types-api.md`) — не створюй окремий ad-hoc контракт.

\- Компоненти — функціональні, з хуками. Класові компоненти не використовуємо, окрім error boundaries.

\- Стан форми конструктора груп полів — тримай передбачувано (не мутуй пропси, не тримай дубльований state, що розходиться з джерелом правди).

\- Стилі — через `@wordpress/components` і CSS logical properties (пам'ятай про RTL-вимогу з ТЗ), уникай хардкоду `left`/`right`.

\- Не тягни нові важкі npm-залежності без обговорення — перевір, чи немає еквівалента вже в `@wordpress/\*`.



\## Безпека — стоп-правила



Перед комітом коду, що торкається збереження/читання даних, перевір:

1\. Чи є санітизація на вході?

2\. Чи є екранування на виході?

3\. Чи є перевірка capability/permission?

4\. Чи є nonce/CSRF-захист для форм?

5\. Чи не потрапляє неперевірений ввід у SQL, `eval`, `include`?



Якщо не впевнений — не вгадуй, познач у відповіді як TODO/питання, а не тихо пропускай.



\## Тестування — вимоги



\- Нова PHP-логіка (збереження/читання поля, валідація, location rules) — обов'язково PHPUnit-тест поруч у `tests/unit/`.

\- Нова E2E-критична дія (створення групи, додавання Repeater-рядка, збереження запису з умовною логікою) — сценарій Playwright у `tests/e2e/`.

\- Не знижуй існуюче покриття тестами. Якщо рефакториш файл — переконайся, що старі тести й далі проходять, онови їх, а не видаляй.

\- Мінімальне покриття core-логіки — 80% (з ТЗ, розділ 4.7). Не приймай PR, що суттєво знижує цей показник.



\## Git / робочий процес



\- Комміти — атомарні, message у форматі `type(scope): опис` (напр. `feat(repeater): add drag-n-drop reordering`, `fix(rest-api): validate permission\_callback for options page`).

\- Не змішуй в одному коміті рефакторинг і нову фічу.

\- Кожна нова фіча з ТЗ — окрема гілка `feature/{назва}`, баг-фікс — `fix/{назва}`.

\- Перед PR: `npm run type-check \&\& npm run lint:js \&\& composer run lint:php \&\& composer run test:unit`.

\- CHANGELOG.md оновлюється в тому ж PR, що й фіча (формат Keep a Changelog).



\## Чого НЕ робити

\- Не копіюй бренд-елементи, назви функцій, іконки чи специфічні текстові рядки з ACF/ACF PRO — юридичний ризик товарних знаків (див. ТЗ, розділ 9 "Ризики").

\- Не додавай кастомні таблиці БД без явного узгодження — за замовчуванням дані йдуть у стандартні `postmeta`/`usermeta`/`termmeta`/`options` (ТЗ, розділ 5.2).

\- Не роби GraphQL-функціонал обов'язковою залежністю ядра — WPGraphQL опційний, перевіряй `class\_exists()` перед хуками.

\- Не редагуй файли в `assets/build/` та `languages/\*.mo` вручну — вони генеруються.

\- Не вимикай strict-режим TypeScript і PHPCS-правила заради "швидше зробити" — якщо правило заважає, обговори зміну самого правила, а не обхід у коді.

\- Не вигадуй нові публічні API-функції/хуки, яких немає в ТЗ, без позначення цього в відповіді — архітектура публічного API узгоджується окремо (розділ 5.4 ТЗ).



\## Коли не впевнений

Якщо задача неоднозначна відносно ТЗ (`docs/TOR.md`) — онов його трактування вголос у відповіді ("я припускаю X, бо ТЗ каже Y") і продовжуй з розумним припущенням, а не зупиняйся. Якщо припущення стосується безпеки, зберігання даних або публічного API — став уточнююче питання, а не вгадуй мовчки.

