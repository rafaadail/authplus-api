# AuthPlus API

# AuthPlus API

[![codecov](https://codecov.io/gh/rafaadail/authplus-api/branch/main/graph/badge.svg)](https://codecov.io/gh/rafaadail/authplus-api)
[![Tests](https://github.com/rafaadail/authplus-api/actions/workflows/tests.yml/badge.svg)](https://github.com/rafaadail/authplus-api/actions)

AuthPlus API is a professional JWT-based authentication service built with Laravel, designed to be secure, scalable, and production-ready.  
The project focuses on API best practices, token-based authentication, observability, and clean architecture.

---

## 🚀 Tech Stack

- **PHP:** 8.3
- **Framework:** Laravel 12
- **Authentication:** JWT (Access & Refresh Tokens)
- **API Style:** RESTful
- **Documentation:** OpenAPI / Swagger
- **Logging:** Structured logs (JSON)
- **Observability:** Request tracing, health checks, Grafana + Loki
- **Containerization:** Docker & Docker Compose

---

## 🎯 Project Goals

- Provide a secure and scalable authentication API
- Demonstrate production-grade Laravel API practices
- Implement real-world JWT authentication flows
- Ensure observability and traceability across requests
- Serve as a reusable authentication microservice

---

## 🔐 Authentication Features

- User login with JWT access token
- Refresh token flow
- Token expiration and renewal
- Token revocation support
- Secure password hashing
- Protected routes using middleware
- Role-based access control (RBAC)

---

## 🌐 API Design

- RESTful architecture
- API versioning (`/api/v1`)
- Standardized JSON responses
- Centralized exception handling
- Proper HTTP status code usage
- Strict request validation layer

---

## 📄 API Documentation

- OpenAPI / Swagger specification
- Bearer authentication support
- Request/response examples
- Documented error contracts

---

## 📊 Observability & Logging

- Structured JSON logging
- Correlation ID per request
- Context-aware logs (user, request, error context)
- Health check endpoint (/health)
- Grafana dashboards for monitoring
- Loki for centralized log aggregation
- Promtail for log shipping

---

## 🧪 Testing

- Unit tests for domain/services
- Feature tests for authentication flows
- Validation of edge cases and failures
- Focus on critical authentication paths
- Continuous improvements in coverage

---

## ⚙️ CI/CD & Code Quality

- GitHub Actions for automated testing
- Codecov integration for test coverage tracking
- Coverage reports generated on every push and pull request
- Quality visibility directly on pull requests

---

## 🐳 Infrastructure

- Fully Dockerized environment
- Docker Compose orchestration
- Environment-based configuration (.env separation)
- Stateless application design

---

## 📌 Project Status

🟢 Core authentication system completed and stable
🚧 Active development on observability, RBAC expansion, and security hardening

---

## 🛣️ Roadmap

- [x] Login endpoint
- [x] Generate JWT
- [x] Refresh token endpoint
- [x] Logout and token revocation
- [x] JWT middleware
- [x] Swagger/OpenAPI documentation
- [x] Structured logging improvements
- [x] Rate limiting
- [x] Test coverage improvements
- [x] Docker Compose setup

---

## 📜 License

This project is open-source and available under the MIT license.
