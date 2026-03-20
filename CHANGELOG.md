# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-03-20

### Added
- Shipment management (create, find, return, exchange, redirect orders)
- Order tracking (status history, bulk status check)
- Shipping rate calculation with 4 delivery types (Door2Door, Branch2Door, Door2Branch, Branch2Branch)
- Branch listing with details
- Order comments (get, add, bulk comments)
- Support ticket management (create, close, COD transfer tickets)
- Staff listing with search and pagination
- Webhook parsing with typed `Webhook` resource
- EventDispatcher with handler classes for webhook event routing
- User-Agent validation for webhook security
- Domain-specific exceptions (AuthenticationException, ValidationException, NotFoundException, WebhookException)
- PHP-CS-Fixer configuration for automated PSR-12 code style enforcement
- Comprehensive test suite with mocked HTTP responses and fixture files
