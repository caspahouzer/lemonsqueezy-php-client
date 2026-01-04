# Contributing to LemonSqueezy PHP API Client

Thank you for your interest in contributing! This document provides guidelines and instructions for contributing to the LemonSqueezy PHP API Client project.

## Code of Conduct

We are committed to providing a welcoming and inspiring community for all. Please read and follow our Code of Conduct.

## How to Contribute

### Reporting Bugs

Before creating bug reports, check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps which reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed after following the steps**
- **Explain which behavior you expected to see instead and why**
- **Include PHP version and OS information**

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. Please include:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and expected behavior**
- **Explain why this enhancement would be useful**

### Pull Requests

- Fill in the required template
- Follow the PHP coding standards
- Include appropriate test cases
- Update documentation
- End all files with a newline
- Avoid platform-specific code

## Development Setup

### Requirements

- PHP 8.0 or higher
- Composer
- Git

### Installation

1. Fork and clone the repository:
```bash
git clone https://github.com/yourusername/lemonsqueezy-php-client.git
cd lemonsqueezy-php-client
```

2. Install dependencies:
```bash
composer install
```

3. Run tests:
```bash
composer test
```

4. Run PHPStan analysis:
```bash
composer stan
```

5. Fix code style issues:
```bash
composer cs:fix
```

## Coding Standards

This project follows PSR-12 coding standards and PSR-4 autoloading.

### Key Guidelines

- Use 4 spaces for indentation
- Use type hints for all parameters and return types
- Use meaningful variable and method names
- Add docblocks to all public methods
- Keep methods small and focused
- Avoid deeply nested conditionals

### Example

```php
/**
 * Get a resource by ID
 *
 * @param string $id Resource ID
 * @param array $options Additional options
 * @return AbstractModel The resource
 * @throws NotFoundException If resource not found
 */
public function get(string $id, array $options = []): AbstractModel
{
    // Implementation
}
```

## Testing

All contributions must include tests:

### Unit Tests

Place unit tests in `tests/Unit/` directory:

```bash
composer test
```

### Integration Tests

Place integration tests in `tests/Integration/` directory.

Set up your test environment:

```bash
cp .env.example .env.local
# Edit .env.local with your test credentials
```

Run integration tests:

```bash
composer test:integration
```

### Test Coverage

Aim for at least 80% code coverage:

```bash
composer test:coverage
```

## Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line
- Consider starting the commit message with an applicable emoji:
  - 🎨 `:art:` when improving the format/structure of the code
  - ⚡ `:zap:` when improving performance
  - 🐛 `:bug:` when fixing a bug
  - ✨ `:sparkles:` when adding a feature
  - 📚 `:books:` when adding or updating documentation
  - 🚀 `:rocket:` when deploying stuff
  - 🔒 `:lock:` when dealing with security
  - ⬆️ `:arrow_up:` when upgrading dependencies
  - ⬇️ `:arrow_down:` when downgrading dependencies
  - 🔧 `:wrench:` when adding/updating configuration files
  - 🗑️ `:wastebasket:` when removing code or files

## Documentation

All public methods must be documented with docblocks. Update README.md and relevant documentation when adding features.

### Documentation Structure

- **README.md** - Project overview and quick start
- **docs/INSTALLATION.md** - Installation instructions
- **docs/QUICKSTART.md** - Getting started guide
- **docs/API_RESOURCES.md** - Resource documentation
- **docs/ERROR_HANDLING.md** - Exception handling guide
- **examples/** - Working code examples

## Release Process

1. Update version numbers in relevant files
2. Update CHANGELOG.md with release notes
3. Create a git tag: `git tag -a vX.Y.Z -m "Release version X.Y.Z"`
4. Push tag: `git push origin vX.Y.Z`
5. Create GitHub release with release notes

## Questions?

Feel free to open an issue or contact the maintainers.

Thank you for contributing! 🚀
