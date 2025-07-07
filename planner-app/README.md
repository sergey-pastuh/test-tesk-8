# 📝 Laravel Task Planning API

A RESTful task management system built with Laravel 11, Docker, Breeze auth, recursive subtasks, strong typing, and clean architecture (DTOs, Enums, Service/Repository layers).

---

## 🐳 Setup Instruction

```bash
cp .env.example .env
cp .env.docker.example .env.docker

# Start containers, install dependencies, migrate and seed DB
make up-n-install
```
---

## 🔐 Demo Credentials
Email:    user@example.com
Password: password

---

## 📚 API Documentation
Generated with Scribe:

- HTML: http://localhost/docs

- OpenAPI (Swagger): http://localhost/docs/openapi.yaml

To regenerate:
```bash
php artisan scribe:generate
```

---

## 🧪 API Endpoints Summary

| Method | URL                        | Description                               |
| ------ | -------------------------- | ----------------------------------------- |
| GET    | `/api/tasks`               | Paginated root tasks with nested subtasks |
| POST   | `/api/tasks`               | Create a new task                         |
| PUT    | `/api/tasks/{id}`          | Update an existing task                   |
| DELETE | `/api/tasks/{id}`          | Delete a task (if not completed)          |
| POST   | `/api/tasks/{id}/complete` | Mark task as completed                    |


---

## 🧱 Architecture
🧩 ValueObjects/ — DTOs + Enums

📂 Repositories/ — DB querying logic

🧠 Services/ — Business logic enforcement

🔒 Policies/ — Per-user access control

🧾 Resources/ — API formatting + Scribe examples

📄 Http/Requests/ — Validation using FormRequest

🧪 Seeders/ — Recursive test task generation



