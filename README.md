# School Management — DDD aplicat en PHP

Projecte A2: Gestor d'Escola construït sobre el projecte base Library del professor,
aplicant **Domain-Driven Design (DDD)** i **MVC**.

---

## 📂 Estructura completa del projecte

```
school-project/
│
├── src/
│   │
│   ├── Domain/                         ← NOU: Domini de l'Escola
│   │   ├── Student/                    ← Entitat + VO (StudentId, Email)
│   │   ├── Course/                     ← Entitat + VO (CourseId)
│   │   ├── Subject/                    ← Entitat + VO (SubjectId)
│   │   ├── Teacher/                    ← Entitat + VO (TeacherId)
│   │   ├── Enrollment/                 ← Agregat (cas d'ús 5)
│   │   └── Assignment/                 ← Agregat (cas d'ús 6)
│   │
│   │   [ORIGINAL DEL PROFESSOR — NO MODIFICAT]
│   │   ├── Domain/Book/                ← Book, BookId, BookRepository
│   │   ├── Domain/User/                ← User, UserId, UserRepository
│   │   └── Domain/Loan/                ← Loan, LoanId, LoanRepository
│   │
│   ├── Application/                    ← NOU: Casos d'ús de l'Escola
│   │   ├── CreateStudent/              ← Command + Handler
│   │   ├── CreateCourse/               ← Command + Handler
│   │   ├── CreateSubject/              ← Command + Handler
│   │   ├── CreateTeacher/              ← Command + Handler
│   │   ├── EnrollStudent/              ← Command + Handler [UC5]
│   │   └── AssignTeacherToSubject/     ← Command + Handler [UC6]
│   │
│   │   [ORIGINAL DEL PROFESSOR — NO MODIFICAT]
│   │   ├── Application/BorrowBook/
│   │   ├── Application/ReturnBook/
│   │   └── Application/RegisterUser/
│   │
│   ├── Infrastructure/
│   │   └── Persistence/
│   │       └── InMemory/               ← NOU: 6 repositoris InMemory
│   │
│   └── Http/                           ← NOU: MVC
│       ├── Controllers/
│       │   └── SchoolController.php
│       └── Views/
│           ├── student/, teacher/, course/, subject/
│           ├── enroll/  ← Cas d'ús 5
│           └── assign/  ← Cas d'ús 6
│
├── tests/
│   ├── Domain/                         ← NOU + ORIGINAL del professor
│   │   ├── Student/, Course/, Subject/
│   │   ├── Teacher/, Enrollment/, Assignment/
│   │   ├── Book/  [professor]
│   │   ├── User/  [professor]
│   │   └── Loan/  [professor]
│   │
│   └── Application/                    ← NOU + ORIGINAL del professor
│       ├── EnrollStudentTest.php       ← UC5
│       ├── AssignTeacherToSubjectTest.php ← UC6
│       ├── BorrowBookTest.php  [professor]
│       ├── ReturnBookTest.php  [professor]
│       └── RegisterUserTest.php [professor]
│
├── index.php                           ← Front Controller (Router MVC)
├── bootstrap.php                       ← [professor] Configuració Doctrine
├── composer.json
└── phpunit.xml
```

---

## 🎯 Casos d'ús implementats

| # | Cas d'ús | Command | Handler |
|---|----------|---------|---------|
| 1 | CreateStudent | CreateStudentCommand | CreateStudentHandler |
| 2 | CreateCourse | CreateCourseCommand | CreateCourseHandler |
| 3 | CreateSubject | CreateSubjectCommand | CreateSubjectHandler |
| 4 | CreateTeacher | CreateTeacherCommand | CreateTeacherHandler |
| **5** | **EnrollStudent** | EnrollStudentCommand | EnrollStudentHandler |
| **6** | **AssignTeacherToSubject** | AssignTeacherToSubjectCommand | AssignTeacherToSubjectHandler |

---

## 🌐 Rutes de l'aplicació (MVC)

```
GET  index.php?route=student          → Llistar estudiants
GET  index.php?route=student/create   → Formulari nou estudiant
POST index.php?route=student/store    → Guardar estudiant

GET  index.php?route=teacher          → Llistar professors
GET  index.php?route=teacher/create   → Formulari nou professor
POST index.php?route=teacher/store    → Guardar professor

GET  index.php?route=course           → Llistar cursos
GET  index.php?route=course/create    → Formulari nou curs
POST index.php?route=course/store     → Guardar curs

GET  index.php?route=subject          → Llistar assignatures
GET  index.php?route=subject/create   → Formulari nova assignatura
POST index.php?route=subject/store    → Guardar assignatura

GET  index.php?route=enroll           → Pàgina matriculació [UC5]
POST index.php?route=enroll/store     → Matricular estudiant [UC5]

GET  index.php?route=assign           → Pàgina assignació [UC6]
POST index.php?route=assign/store     → Assignar professor [UC6]
```

---

## 🧪 Executar els tests

```bash
# Instal·lar dependències
composer install

# Tests de domini (PURS: sense mocks, sense BD, ultra ràpids)
# Inclou els del professor + els nous de l'escola
vendor/bin/phpunit --testsuite=domain

# Tests d'aplicació (amb MOCKS: UC5 i UC6 + els del professor)
vendor/bin/phpunit --testsuite=application

# TOTS els tests
vendor/bin/phpunit
```

---

## 🏗️ Principis DDD aplicats

**Value Objects**: `StudentId`, `CourseId`, `SubjectId`, `TeacherId`, `EnrollmentId`,
`AssignmentId`, `Email` (extra)

**Entitats**: `Student`, `Course`, `Subject`, `Teacher`

**Agregats**: `Enrollment` (UC5), `Assignment` (UC6) — amb Factory Methods

**Repositoris**: Interfície al Domini, implementació InMemory a la Infraestructura

**Application Services**: Un Command + Handler per cas d'ús

**Invariants al domini**: Validacions als constructors i Value Objects

**InMemory**: Dades en memòria, canviable per Doctrine sense tocar Domini ni Aplicació

---

## ⭐ Extras implementats (suma nota)

- Value Object `Email` amb validació de format (`filter_var`)
- Mètode `equals()` als Value Objects
- Factory Methods als Agregats (`Enrollment::enroll()`, `Assignment::assign()`)
- Efectes laterals delegats al domini (`subject->assignTeacher()` dins `Assignment::assign()`)
- Tests de domini **100% purs** (sense mocks — piràmide de tests correcta)
- Comentaris extensius a tots els fitxers

---

## 🔐 Part 3 — Autenticació OAuth (GitHub)

### Arquitectura

L'autenticació OAuth 2.0 s'ha implementat en **dos nivells**:

1. **SPA (frontend)** — Login via GitHub OAuth, sessió PHP
2. **API REST (backend)** — Bearer Token Authentication

```
school-project/
├── spa/
│   ├── app/Http/
│   │   ├── Controllers/
│   │   │   └── AuthController.php      ← Flux OAuth (login, callback, logout)
│   │   └── Middleware/
│   │       └── AuthMiddleware.php      ← Protecció de rutes SPA
│   └── resources/views/auth/
│       └── login.php                   ← Pàgina de login
│
├── src/Http/Middleware/
│   └── ApiAuthMiddleware.php           ← Bearer token per a l'API REST
│
├── .env.example                        ← Variables d'entorn necessàries
└── README.md
```

### Flux OAuth (SPA)

```
Usuari → GET /login
       → GET /auth/github  → GitHub OAuth
       ← GET /auth/callback?code=...
       → Sessió establerta → redirigeix a /
       
GET /logout → Destrueix sessió → redirigeix a /login
```

### Configuració

1. Copia `.env.example` a `.env`:
   ```bash
   cp .env.example .env
   ```

2. Crea una **GitHub OAuth App** a:
   [https://github.com/settings/developers](https://github.com/settings/developers)
   - **Homepage URL**: `http://localhost:8000`
   - **Authorization callback URL**: `http://localhost:8000/auth/callback`

3. Omple el `.env`:
   ```
   APP_URL=http://localhost:8000
   GITHUB_CLIENT_ID=el_teu_client_id
   GITHUB_CLIENT_SECRET=el_teu_client_secret
   API_TOKEN=genera_un_token_segur
   ```

4. Arrenca el servidor:
   ```bash
   php -S localhost:8000
   ```

### Protecció de l'API REST

Totes les peticions a `/api/*` requereixen un **Bearer token**:

```bash
# Amb el token estàtic del .env (recomanat per a Postman/tests)
curl -H "Authorization: Bearer el_teu_api_token" \
     http://localhost:8000/api/students

# O amb query param per a proves ràpides
curl "http://localhost:8000/api/students?api_token=el_teu_api_token"
```

### Rutes d'autenticació (SPA)

| Mètode | Ruta | Descripció |
|--------|------|------------|
| GET | `/login` | Pàgina de login |
| GET | `/auth/github` | Inicia el flux OAuth |
| GET | `/auth/callback` | Callback de GitHub |
| GET | `/logout` | Tanca la sessió |

