## [1.7.0] - 2026-10-12

### 🚀 Features

- Create and update articles (`Article::create()` via `POST /articles`, `Article::update()` via `PUT /articles/{id}`); `update()` fetches the current `version` uncached for optimistic locking unless one is passed
- Add `put()` to `ClientInterface` and `Client` (same retry, rate limiting and error handling as `post()`, never cached)

## [1.6.0] - 2026-10-11

### 🚀 Features

- Add Articles support (`ArticleService`, `Article` facade) for `GET /articles` and `GET /articles/{id}`
- Support Laravel 13 (`illuminate/*` ^13.0, testbench 11, Pest 5)

### 🐛 Bug Fixes

- Scope cached GET responses per API key so clients for different accounts never share cached data

## [1.5.1] - 2025-09-18

### 🚀 Features

- Implement withoutCache method across services and facades for cache bypassing, including unit tests
## [1.5.0] - 2025-09-18

### 🚀 Features

- Add withoutCache method to Client for bypassing cache on requests and corresponding unit tests

### ⚙️ Miscellaneous Tasks

- *(release)* V1.5.0
## [1.4.0] - 2025-09-18

### 🚀 Features

- Add all method to InvoiceService for retrieving invoices with optional filters and corresponding unit tests

### ⚙️ Miscellaneous Tasks

- Add composer test:do
## [1.3.1] - 2025-09-16

### 🚀 Features

- Make compatible with Laravel 12
- Enhance ApiException with status code and response data
- Add caching support to client and service provider
- Implement rate limiting with token bucket algorithm
- Add retry mechanism with exponential backoff
- Add comprehensive validation to InvoiceBuilder
- Add DTOs for Contact, Invoice, Dunning and Country
- Extend configuration with timeout, retry, cache and logging options
- Improve tests with mock factory and additional test cases
- Improve IDE support with facade annotations and phpstorm meta
- Enhance Client and InvoiceBuilder with improved error handling and validation

### 🚜 Refactor

- Remove unused DTOs and enhance Client error handling with cache support

### 📚 Documentation

- Enhance documentation with new features and examples
- Simplify README to focus on Laravel-typical facade usage
## [1.2.0] - 2025-09-08

### 🚀 Features

- Add dunnings
## [1.1.3] - 2025-09-08

### 🚜 Refactor

- *(Client)* Make base URL configurable and improve error handling

### ⚙️ Miscellaneous Tasks

- Update Changelog
## [1.1.2] - 2025-09-08

### 🐛 Bug Fixes

- Update Client.php and ClientTest.php to get 100% test coverage
## [1.1.1] - 2025-06-01

### ⚙️ Miscellaneous Tasks

- *(release)* Add changelog for v1.1.0
- Better exception logging in client
## [1.1.0] - 2025-01-21

### 🚀 Features

- *(InvoiceBuilder)* Add forContact method for direct contact ID assignment
## [1.0.1] - 2025-01-09

### 🐛 Bug Fixes

- Allow float for quantity
## [1.0.0] - 2025-01-09

### 🚀 Features

- Add builder pattern
- Add supplement

### ⚙️ Miscellaneous Tasks

- Init
- Add shipping costs to README
