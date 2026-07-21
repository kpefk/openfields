\# Технічне завдання



\## OpenFields for WordPress

\*\*Open Source плагін для кастомних полів, груп полів та конструкторів контенту\*\*



Версія документа: 1.0

Дата: 21.07.2026



\---



\## 1. Загальна інформація



\### 1.1 Назва проєкту

OpenFields for WordPress (робоча назва, надалі — «OpenFields» або «Плагін»)



\### 1.2 Мета проєкту

Розробити повнофункціональний Open Source плагін для WordPress, що є вільною альтернативою Advanced Custom Fields PRO (ACF PRO), надаючи можливості створення кастомних полів, груп полів, репітерів, гнучкого контенту, опцій сторінок, а також глибоку інтеграцію з Gutenberg, REST API та GraphQL — без обмежень пропрієтарної ліцензії.



\### 1.3 Проблема, яку вирішує проєкт

\- ACF PRO є платним для розширених функцій (репітери, гнучкий контент, клонування полів, опції сторінок, галерея).

\- Закриті ліцензійні обмеження ускладнюють використання в SaaS, багатосайтових мережах, дистрибутивах тем/плагінів.

\- Ринку потрібен інструмент з ідентичним UX, але GPL-сумісний, з можливістю форку та кастомізації коду ком'юніті.



\### 1.4 Цільова аудиторія

\- Розробники WordPress (агенції, фрілансери).

\- Автори тем і плагінів, яким потрібні кастомні поля «з коробки».

\- Власники сайтів на headless WordPress (React/Next.js/Vue) через REST/GraphQL.

\- Open Source ком'юніті та контриб'ютори.



\### 1.5 Ліцензія

GPL v2 або новіша (сумісна з WordPress core). Код публікується на GitHub, розповсюдження — через wordpress.org/plugins та Composer/Packagist.



\---



\## 2. Огляд функціональності (Scope)



\### 2.1 MVP (Фаза 1)

1\. Конструктор груп полів (Field Group Builder) в адмінці.

2\. Базові типи полів (13 шт., див. розділ 3.1).

3\. Правила розташування (Location Rules) — прив'язка груп полів до типів записів, сторінок, таксономій, шаблонів.

4\. Відображення полів у метабоксах класичного редактора та в Gutenberg (Sidebar Panel).

5\. Базовий REST API (читання значень полів).

6\. Імпорт/експорт груп полів у JSON/PHP.

7\. Локалізація (i18n) інтерфейсу, підтримка перекладів через .pot файл.



\### 2.2 Фаза 2 — «PRO»-функції у Free-версії

1\. Репітер (Repeater Field).

2\. Гнучкий контент (Flexible Content Field).

3\. Клонування полів (Clone Field).

4\. Опції сторінок (Options Pages / Global Settings).

5\. Поле «Галерея» з drag-n-drop сортуванням.

6\. Умовна логіка (Conditional Logic) для показу/приховування полів.

7\. Gutenberg-блоки на основі груп полів (ACF Blocks аналог) — реєстрація кастомних блоків через PHP/JSON без написання JS.



\### 2.3 Фаза 3 — Розширення екосистеми

1\. Повноцінна GraphQL-схема (інтеграція з WPGraphQL).

2\. Field Types API — можливість реєстрації кастомних типів полів сторонніми розробниками.

3\. Візуальний конструктор блоків (block.json генератор).

4\. Синхронізація груп полів між середовищами (Local ⇄ Staging ⇄ Production) через JSON-файли в темі.

5\. ACF-мігратор — імпорт існуючих даних та конфігурацій з ACF/ACF PRO.

6\. REST API запис/оновлення значень полів (не тільки читання).

7\. Права доступу (Capabilities) — обмеження редагування полів за ролями користувачів.



\### 2.4 Поза межами проєкту (Out of Scope)

\- Конструктор форм для фронтенду (form builder) — окремий плагін у майбутньому.

\- Візуальний page builder (Elementor-подібний).

\- E-commerce специфічні поля (WooCommerce інтеграція — окремий аддон).



\---



\## 3. Функціональні вимоги



\### 3.1 Типи полів (Field Types)



| # | Тип поля | Опис | Фаза |

|---|----------|------|------|

| 1 | Text | Однорядкове текстове поле | 1 |

| 2 | Textarea | Багаторядкове текстове поле | 1 |

| 3 | Number | Числове поле з min/max/step | 1 |

| 4 | Email | Поле з валідацією email | 1 |

| 5 | URL | Поле з валідацією URL | 1 |

| 6 | Password | Поле паролю | 1 |

| 7 | Image | Вибір зображення з медіатеки | 1 |

| 8 | File | Вибір файлу з медіатеки | 1 |

| 9 | WYSIWYG Editor | TinyMCE редактор | 1 |

| 10 | Select | Випадаючий список (одиничний/множинний) | 1 |

| 11 | Checkbox | Група чекбоксів | 1 |

| 12 | Radio Button | Група радіокнопок | 1 |

| 13 | True/False | Перемикач (toggle) | 1 |

| 14 | Date Picker | Вибір дати | 1 |

| 15 | Date Time Picker | Вибір дати й часу | 1 |

| 16 | Color Picker | Вибір кольору | 1 |

| 17 | Link | Посилання (URL + текст + target) | 1 |

| 18 | Post Object | Зв'язок із записом (relational) | 1 |

| 19 | Page Link | Посилання на сторінку/запис | 1 |

| 20 | Taxonomy | Вибір терміна таксономії | 1 |

| 21 | User | Вибір користувача | 1 |

| 22 | Relationship | Множинний зв'язок із записами (drag-n-drop) | 1 |

| 23 | Range | Повзунок (slider) | 1 |

| 24 | Group | Групування полів у структурі (без повторення) | 1 |

| 25 | Repeater | Повторювана група полів | 2 |

| 26 | Flexible Content | Набір з макетів (layouts), що додаються довільно | 2 |

| 27 | Clone | Клонування полів/груп із інших наборів | 2 |

| 28 | Gallery | Множинний вибір зображень із сортуванням | 2 |

| 29 | Google Map / OSM | Координати на карті (OpenStreetMap за замовчуванням) | 2 |

| 30 | Accordion | Візуальне групування в адмінці (UI-only) | 2 |

| 31 | Tab | Вкладки для групування полів у формі | 2 |

| 32 | Message | Інформаційне повідомлення (UI-only, без даних) | 1 |

| 33 | Custom (API) | Кастомний тип поля через Field Type API | 3 |



\*\*Вимоги до кожного типу поля:\*\*

\- Унікальний `name` (key) та `label`.

\- Підтримка `instructions`, `required`, `default\_value`, `placeholder`.

\- Умовна логіка показу.

\- Валідація на фронтенді (JS) та бекенді (PHP) окремо.

\- Wrapper-атрибути: width (%), class, id.



\### 3.2 Конструктор груп полів (Field Group Builder)



\- Drag-n-drop інтерфейс упорядкування полів (на базі HTML5 Sortable/interact.js, без jQuery UI, де можливо).

\- Вкладені поля (для Repeater/Group/Flexible Content) — необмежена вкладеність (з обмеженням продуктивності — попередження при > 5 рівнів).

\- Дублювання поля/групи одним кліком.

\- Масові дії: увімкнути/вимкнути, видалити, дублювати групу.

\- Налаштування групи:

&#x20; - Position (normal, side, acf\_after\_title).

&#x20; - Style (default / seamless — без WP-метабокс обгортки).

&#x20; - Label placement (top / left).

&#x20; - Instruction placement (label / field).

&#x20; - Order (пріоритет відображення відносно інших груп).

&#x20; - Permissions (показувати/приховувати "Custom Fields" стандартний метабокс WP).

&#x20; - Description (внутрішня документація для розробників).



\### 3.3 Правила розташування (Location Rules)



Підтримка умов (AND/OR групи правил):

\- Post Type (є / не є).

\- Page Template.

\- Page Parent / Page Type (front page, top level, etc.).

\- Post Category / Post Taxonomy Term.

\- Post Format.

\- Post Status.

\- User Role.

\- Current User (logged in).

\- Taxonomy edit screen (архівна сторінка таксономії).

\- Attachment (за типом медіафайлу).

\- Comment.

\- Widget.

\- Menu / Menu Item.

\- Block editor (Gutenberg) vs Classic editor.

\- Options Page (кастомна сторінка налаштувань).



\### 3.4 Умовна логіка (Conditional Logic)



\- Показати/приховати поле, якщо значення іншого поля (в межах тієї ж групи/repeater-рядка) відповідає умові: `==`, `!=`, `>`, `<`, `matches pattern (regex)`, `contains`, `empty`, `not empty`.

\- Комбінування умов через AND/OR.

\- Робота як для top-level полів, так і всередині Repeater/Flexible Content/Group.



\### 3.5 Gutenberg інтеграція



1\. \*\*Sidebar Panel\*\* — поля групи відображаються в бічній панелі редактора блоків (аналог поточного ACF-поводження).

2\. \*\*ACF Blocks аналог (OpenFields Blocks)\*\*:

&#x20;  - Реєстрація блоку через `block.json` + PHP callback або через `render.php` шаблон (аналог `acf\_register\_block\_type`).

&#x20;  - Підтримка `InnerBlocks` (вкладені блоки).

&#x20;  - Підтримка попереднього перегляду в редакторі (Live Preview) без перезавантаження.

&#x20;  - Кешування рендеру блоку (опційно).

3\. Підтримка Block Bindings API (WordPress 6.5+) для прив'язки полів напряму до атрибутів нативних блоків (параграф, зображення тощо) — без потреби писати кастомний блок.



\### 3.6 REST API



\- Ендпоінт: `GET /wp-json/openfields/v1/{post\_type}/{id}` — повертає значення всіх полів запису.

\- Реєстрація полів у стандартній схемі WP REST API (`register\_rest\_field`) — щоб поля з'являлись у стандартних ендпоінтах `/wp/v2/posts` тощо, під ключем `openfields\_fields` (конфігуровано).

\- Фаза 3: `POST/PUT` для запису значень (з перевіркою прав доступу через `permission\_callback`).

\- Підтримка форматованих (formatted) і сирих (raw) значень через query-параметр `?format=raw|formatted`.



\### 3.7 GraphQL



\- Офіційний коннектор для WPGraphQL: автоматична реєстрація типів полів у GraphQL-схемі на основі конфігурації груп полів.

\- Підтримка Repeater/Flexible Content як вкладених GraphQL-типів (union types для Flexible Content layouts).

\- Приклад запиту документується в README.



\### 3.8 Імпорт / Експорт



\- Експорт обраних груп полів у JSON (сумісний формат) або згенерований PHP-код (`acf\_add\_local\_field\_group`-подібна функція `openfields\_register\_field\_group()`).

\- Імпорт JSON через drag-n-drop або файловий диспетчер.

\- Автоматична синхронізація: якщо в темі/плагіні є `openfields-json/` папка — групи реєструються "локально" (local JSON), з можливістю синхронізації в базу даних одним кліком (аналог ACF Local JSON).



\### 3.9 Опції сторінок (Options Pages)



\- API для реєстрації кастомних сторінок налаштувань (`openfields\_add\_options\_page()`).

\- Підтримка ієрархії (батьківська сторінка + дочірні вкладки).

\- Значення зберігаються в `wp\_options` (autoload за потреби).



\### 3.10 Права доступу та безпека



\- Nonce-перевірка для всіх форм збереження.

\- Санітизація/екранування усіх вхідних і вихідних даних відповідно до типу поля.

\- Капабіліті (`edit\_field\_groups`, `manage\_options\_pages`) для розмежування доступу в мультикористувацьких середовищах.

\- Опційне обмеження, які ролі можуть редагувати конкретну групу полів.



\---



\## 4. Нефункціональні вимоги



\### 4.1 Продуктивність

\- Завантаження сторінки редагування запису з 50+ полями — не більше +150 мс до базового часу рендеру WP-адмінки.

\- Кешування метаданих через `wp\_cache` (object cache) для повторних запитів `get\_field()`.

\- Lazy-loading JS для типів полів, що не використовуються на поточному екрані (напр. Google Maps SDK).



\### 4.2 Сумісність

\- WordPress: остання стабільна версія та дві попередні мажорні (мінімум WP 6.4+).

\- PHP: 7.4–8.3 (з таргетуванням на 8.1+ як рекомендованого).

\- MySQL 5.7+ / MariaDB 10.3+.

\- Браузери адмінки: останні 2 версії Chrome, Firefox, Safari, Edge.

\- Сумісність з популярними темами (Astra, GeneratePress) та плагінами (WooCommerce, Yoast SEO, WPML/Polylang) — регресійне тестування.



\### 4.3 Безпека

\- Відповідність WordPress Plugin Security Guidelines.

\- Регулярний статичний аналіз (PHPCS з WordPress Coding Standards, PHPStan рівень 6+).

\- Відсутність прямих SQL-запитів без `$wpdb->prepare()`.

\- Захист від XSS/CSRF/SQL-injection — покриття тестами.



\### 4.4 Локалізація (i18n/l10n)

\- Весь текстовий контент через `\_\_()`/`\_e()` з text domain `openfields`.

\- Генерація `.pot` файлу автоматично через build-скрипт.

\- Готовність до перекладу через translate.wordpress.org.

\- RTL-підтримка в адмін-інтерфейсі (CSS logical properties).



\### 4.5 Доступність (Accessibility)

\- Відповідність WCAG 2.1 AA для адмін-інтерфейсу.

\- Керування клавіатурою (drag-n-drop має keyboard-фолбек через кнопки "вгору/вниз").

\- ARIA-атрибути для кастомних UI-компонентів.



\### 4.6 Розширюваність

\- Хуки `do\_action`/`apply\_filters` на кожному ключовому етапі (реєстрація поля, збереження значення, рендер поля, валідація).

\- Публічний PHP API, задокументований через PHPDoc + генерація документації (phpDocumentor).

\- Field Type API — інтерфейс `OpenFields\\FieldType` для реєстрації кастомних типів сторонніми розробниками.



\### 4.7 Тестування

\- Unit-тести (PHPUnit) — покриття core-логіки (збереження/читання полів, валідація) не менше 80%.

\- Статична перевірка типів TypeScript (`tsc --noEmit`, strict mode) — обов'язковий крок CI, помилки типів блокують мердж.

\- Інтеграційні тести (WP-CLI + wp-env) для сценаріїв Location Rules.

\- E2E-тести (Playwright) для критичних UI-флоу: створення групи, додавання Repeater, збереження запису.

\- CI/CD через GitHub Actions: лінтинг (ESLint + `@wordpress/eslint-plugin` з TS-правилами, PHPCS), type-check, тести, збірка релізного zip.



\---



\## 5. Архітектура рішення



\### 5.1 Технологічний стек



| Шар | Технологія |

|-----|-----------|

| Backend | PHP 8.1+ (OOP, PSR-4 автозавантаження через Composer) |

| Адмін UI | TypeScript + React (`.tsx`, використовуючи `@wordpress/element`, `@wordpress/components` — той самий стек, що й Gutenberg, для консистентності) |

| Типізація | TypeScript 5.x (strict mode), типи `@types/wordpress\_\_\*`, `@types/react` |

| Збірка фронтенду | `@wordpress/scripts` (webpack під капотом, вбудована підтримка TS через офіційний `tsconfig` пресет) |

| База даних | Стандартні таблиці WP (`wp\_postmeta`, `wp\_options`, `wp\_termmeta`, `wp\_usermeta`) — без кастомних таблиць у MVP |

| API | WP REST API, WPGraphQL (опційна залежність) |

| Тестування | PHPUnit, Playwright, WP-CLI |

| CI/CD | GitHub Actions |

| Розповсюдження | wordpress.org/plugins (SVN-синхронізація), GitHub Releases, Composer (packagist.org) |



\### 5.2 Структура даних



\- Групи полів зберігаються як Custom Post Type `openfields-group` (аналог ACF), `post\_status = publish/disabled`.

\- Значення полів зберігаються стандартно в `postmeta`/`usermeta`/`termmeta`/`options` залежно від контексту, з підтримкою серіалізації для складних типів (Repeater/Flexible Content — через префіксовані ключі `field\_0\_subfield`, сумісно за підходом з ACF для полегшення міграції).

\- Для Фази 3 розглядається опційний режим зберігання в JSON-колонці (custom table) для покращення продуктивності запитів по вкладених полях — під фіче-флагом, не за замовчуванням (щоб зберегти зворотну сумісність).



\### 5.3 Модульна структура плагіна



```

openfields/

├── openfields.php                 # bootstrap-файл плагіна

├── composer.json

├── package.json

├── includes/

│   ├── Core/                      # ядро: реєстрація CPT, автозавантаження

│   ├── FieldTypes/                # класи типів полів (по одному класу на тип)

│   ├── FieldGroups/                # логіка груп полів, location rules

│   ├── Admin/                     # адмін-екрани, React-додатки

│   ├── Api/

│   │   ├── Rest/                  # REST-контролери

│   │   └── GraphQL/               # GraphQL-резолвери

│   ├── Blocks/                    # реєстрація Gutenberg-блоків

│   ├── ImportExport/

│   └── Support/                   # хелпери, санітизація, кешування

├── assets/

│   ├── src/                       # вихідний TypeScript/CSS (React-компоненти, .ts/.tsx)

│   │   ├── field-types/           # компоненти й реєстрація типів полів (Field Type API)

│   │   ├── admin/                 # конструктор груп полів, екрани налаштувань

│   │   └── blocks/                # Gutenberg-блоки (OpenFields Blocks)

│   └── build/                     # скомпільовані файли (JS, sourcemaps, .d.ts за потреби)

├── languages/                     # .pot/.po/.mo

├── tests/

│   ├── unit/

│   ├── integration/

│   └── e2e/

└── docs/

```



\### 5.4 Публічний PHP API (приклади функцій)



```php

openfields\_add\_local\_field\_group( array $config ): void

get\_field( string $selector, $post\_id = false, bool $format\_value = true );

get\_fields( $post\_id = false );

update\_field( string $selector, $value, $post\_id = false );

have\_rows( string $selector, $post\_id = false ): bool;

the\_row(): array;

openfields\_add\_options\_page( array $config ): void;

openfields\_register\_field\_type( string $class ): void;

```



\### 5.5 TypeScript: конфігурація та вимоги



\*\*Обов'язкова вимога:\*\* увесь новий JS-код в адмін-панелі, Gutenberg-блоках і полях-компонентах пишеться на TypeScript (`.ts`/`.tsx`). Чистий JavaScript допускається лише для дрібних legacy-скриптів сумісності (напр. поліфіли для класичного редактора).



\*\*Налаштування збірки:\*\*



```jsonc

// tsconfig.json

{

&#x20; "extends": "@wordpress/scripts/config/tsconfig.json",

&#x20; "compilerOptions": {

&#x20;   "strict": true,

&#x20;   "jsx": "react-jsx",

&#x20;   "baseUrl": ".",

&#x20;   "paths": {

&#x20;     "@fields/\*": \["assets/src/field-types/\*"],

&#x20;     "@admin/\*": \["assets/src/admin/\*"]

&#x20;   }

&#x20; },

&#x20; "include": \["assets/src/\*\*/\*.ts", "assets/src/\*\*/\*.tsx"]

}

```



```json

// package.json (фрагмент)

{

&#x20; "devDependencies": {

&#x20;   "@wordpress/scripts": "^30.0.0",

&#x20;   "typescript": "^5.5.0",

&#x20;   "@types/react": "^18.3.0",

&#x20;   "@types/wordpress\_\_block-editor": "^11.0.0",

&#x20;   "@types/wordpress\_\_components": "^23.0.0",

&#x20;   "@types/wordpress\_\_data": "^6.0.0"

&#x20; },

&#x20; "scripts": {

&#x20;   "build": "wp-scripts build",

&#x20;   "start": "wp-scripts start",

&#x20;   "type-check": "tsc --noEmit"

&#x20; }

}

```



`type-check` запускається окремим кроком у CI (GitHub Actions) паралельно з ESLint і PHPCS — помилки типів блокують мердж у `main`.



\*\*Спільний контракт типів PHP ⇄ TypeScript.\*\*

Щоб уникнути розсинхронізації між PHP REST-схемою та фронтендом, схема поля описується один раз (JSON Schema або PHP-масив) і з неї генеруються `.d.ts` файли build-скриптом (`npm run generate:types`). Це стосується як стандартних значень полів, так і конфігурації груп полів, що передається у React-конструктор.



\*\*Field Type API — базовий контракт для сторонніх розробників (Фаза 3).\*\*

Кожен кастомний тип поля (як вбудований, так і сторонній) реалізує на фронтенді єдиний TypeScript-інтерфейс, що гарантує однакову поведінку в конструкторі груп полів, у Repeater/Flexible Content та в умовній логіці:



```typescript

// assets/src/field-types/types.ts



export interface FieldValue {

&#x20; \[key: string]: unknown;

}



export interface FieldConfig {

&#x20; key: string;

&#x20; name: string;

&#x20; label: string;

&#x20; type: string;

&#x20; instructions?: string;

&#x20; required?: boolean;

&#x20; defaultValue?: unknown;

&#x20; placeholder?: string;

&#x20; conditionalLogic?: ConditionalLogicGroup\[] | false;

&#x20; wrapper?: {

&#x20;   width?: string;

&#x20;   class?: string;

&#x20;   id?: string;

&#x20; };

&#x20; // довільні налаштування, специфічні для конкретного типу поля

&#x20; settings?: Record<string, unknown>;

}



export interface ConditionalLogicRule {

&#x20; field: string;                 // ключ поля, від якого залежимо

&#x20; operator: '==' | '!=' | '>' | '<' | 'contains' | 'empty' | 'not\_empty' | 'matches';

&#x20; value?: unknown;

}



export type ConditionalLogicGroup = ConditionalLogicRule\[]; // AND всередині групи



export interface FieldTypeDefinition<TValue = FieldValue> {

&#x20; /\*\* Унікальний ідентифікатор типу поля, напр. "text", "repeater", "acme\_map" \*/

&#x20; type: string;



&#x20; /\*\* Назва для відображення у списку типів у конструкторі \*/

&#x20; label: string;



&#x20; /\*\* Іконка (dashicon або SVG-компонент) \*/

&#x20; icon?: string | React.ReactNode;



&#x20; /\*\* Категорія в списку вибору типу поля: "basic" | "content" | "choice" | "relational" | "layout" \*/

&#x20; category: 'basic' | 'content' | 'choice' | 'relational' | 'layout';



&#x20; /\*\* React-компонент редагування поля у формі запису \*/

&#x20; EditComponent: React.ComponentType<FieldEditProps<TValue>>;



&#x20; /\*\* React-компонент налаштувань поля у конструкторі груп полів \*/

&#x20; SettingsComponent?: React.ComponentType<FieldSettingsProps>;



&#x20; /\*\* Валідація на фронтенді перед збереженням \*/

&#x20; validate?: (value: TValue, config: FieldConfig) => string | null; // повертає текст помилки або null



&#x20; /\*\* Значення за замовчуванням для нового поля цього типу \*/

&#x20; getDefaultConfig: () => Partial<FieldConfig>;

}



export interface FieldEditProps<TValue = FieldValue> {

&#x20; config: FieldConfig;

&#x20; value: TValue;

&#x20; onChange: (value: TValue) => void;

&#x20; disabled?: boolean;

}



export interface FieldSettingsProps {

&#x20; config: FieldConfig;

&#x20; onChange: (config: FieldConfig) => void;

}

```



Приклад реєстрації кастомного типу поля сторонньою розробкою:



```typescript

import { registerFieldType } from '@openfields/field-types-api';

import type { FieldTypeDefinition } from '@openfields/field-types-api/types';



const RatingField: FieldTypeDefinition<number> = {

&#x20; type: 'acme\_rating',

&#x20; label: 'Рейтинг (зірки)',

&#x20; category: 'choice',

&#x20; EditComponent: ({ value, onChange, disabled }) => (

&#x20;   <StarRatingInput value={value} onChange={onChange} disabled={disabled} />

&#x20; ),

&#x20; getDefaultConfig: () => ({ defaultValue: 0 }),

&#x20; validate: (value) => (value < 0 || value > 5 ? 'Значення має бути від 0 до 5' : null),

};



registerFieldType(RatingField);

```



Такий підхід дозволяє:

\- гарантувати типобезпечність при обміні даними між конструктором груп полів, Repeater/Flexible Content і рушієм умовної логіки;

\- стороннім розробникам отримувати автодоповнення (IntelliSense) і перевірку типів "з коробки" при написанні власних типів полів;

\- уникати дублювання PropTypes-подібних перевірок під час рантайму — більшість помилок ловиться на етапі компіляції (`tsc --noEmit` у CI).



\---



\## 6. UI/UX вимоги



\- Адмін-екран "Field Groups" — список усіх груп із фільтрами за статусом і локацією.

\- Екран редагування групи максимально повторює звичний патерн WP (метабокси, Publish box), щоб знизити поріг входження для колишніх користувачів ACF.

\- Попередній перегляд полів у реальному часі (де можливо) без збереження сторінки.

\- Іконки та термінологія — нейтральні (уникати прямого копіювання брендованих елементів ACF, щоб не порушувати товарні знаки; функціональна поведінка при цьому може бути аналогічною).

\- Темна тема адмінки WP (WP Admin Color Schemes) — коректне відображення компонентів.



\---



\## 7. План релізів (Roadmap)



| Версія | Зміст | Орієнтовний термін |

|--------|-------|---------------------|

| 0.1.0-alpha | Ядро + 13 базових типів полів + Location Rules + метабокси | Місяць 1–2 |

| 0.5.0-beta | Gutenberg Sidebar Panel + REST API (read) + імпорт/експорт JSON | Місяць 3 |

| 1.0.0 | Публічний реліз на wordpress.org, повна документація, i18n | Місяць 4 |

| 1.1.0 | Repeater, Flexible Content, Clone, Gallery, Conditional Logic | Місяць 5–6 |

| 1.2.0 | Options Pages, OpenFields Blocks (ACF Blocks аналог) | Місяць 7 |

| 2.0.0 | GraphQL-інтеграція, Field Type API для сторонніх розробників, REST write-доступ | Місяць 8–10 |

| 2.1.0+ | ACF-мігратор, синхронізація середовищ, розширення ком'юніті | Постійно |



\---



\## 8. Критерії приймання (Acceptance Criteria)



1\. Плагін встановлюється й активується без помилок на чистій інсталяції WordPress останньої стабільної версії.

2\. Створення групи полів із 5+ різними типами полів і прив'язка до Post Type "Записи" — поля коректно відображаються й зберігаються.

3\. Значення, збережені через класичний редактор, коректно читаються через `get\_field()` і REST API.

4\. Repeater з 3 вкладеними полями коректно зберігає й повертає масив рядків.

5\. Умовна логіка приховує/показує залежне поле відповідно до значення батьківського поля без перезавантаження сторінки.

6\. Експортований JSON групи полів успішно імпортується на іншому інсталі й відтворює ідентичну конфігурацію.

7\. Усі автоматизовані тести (unit/integration/e2e) проходять у CI без помилок.

8\. Немає критичних/високих вразливостей за результатами сканування (WPScan / Plugin Check).

9\. Документація (README, Developer docs, Hooks reference) опублікована та відповідає поточній версії коду.



\---



\## 9. Ризики та обмеження



| Ризик | Вплив | Мітигація |

|-------|-------|-----------|

| Товарні знаки ACF (назви функцій/полів) | Юридичний | Використовувати власні назви функцій (`openfields\_\*`), уникати копіювання брендованих текстів/іконок |

| Продуктивність при глибокій вкладеності Repeater | Технічний | Ліміти за замовчуванням + попередження в UI, індексація метаданих |

| Сумісність зі старими сайтами на ACF (міграція) | Продуктовий | Окремий модуль-мігратор у Фазі 3, тестування на реальних дампах даних |

| Залежність від WPGraphQL (плагін третьої сторони) | Технічний | GraphQL-функціонал — опційний аддон, не обов'язкова залежність ядра |

| Підтримка ком'юніті / контриб'юторів | Організаційний | Чіткий CONTRIBUTING.md, шаблони Issues/PR, roadmap на GitHub Projects |



\---



\## 10. Документація та підтримка



\- README.md (встановлення, швидкий старт).

\- CHANGELOG.md за форматом Keep a Changelog.

