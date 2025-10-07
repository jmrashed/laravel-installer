# Contributing to Laravel Installer

Thank you for your interest in contributing to Laravel Installer! We welcome contributions from everyone. By participating in this project, you agree to abide by our [Code of Conduct](CODE_OF_CONDUCT.md).

## How to Contribute

### Reporting Bugs

- Use the GitHub issue tracker to report bugs.
- Describe the bug clearly, including steps to reproduce.
- Include your environment details (PHP version, Laravel version, OS, etc.).
- If possible, provide a minimal test case.

### Suggesting Features

- Use the GitHub issue tracker to suggest new features.
- Provide a clear description of the feature and its use case.
- Explain why this feature would be beneficial to the project.

### Contributing Code

1. Fork the repository on GitHub.
2. Create a new branch for your changes.
3. Make your changes following our coding standards.
4. Write tests for your changes.
5. Ensure all tests pass.
6. Submit a pull request with a clear description of your changes.

## Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/laravel-installer.git
   cd laravel-installer
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Copy the environment file and configure it:
   ```bash
   cp .env.example .env
   ```

4. Run the tests:
   ```bash
   ./vendor/bin/phpunit
   ```

## Coding Standards

- Follow PSR-12 coding standards.
- Use meaningful variable and method names.
- Write clear, concise comments.
- Keep lines under 120 characters.
- Use type hints where possible.

## Testing

- Write unit tests for new functionality.
- Write feature tests for new features.
- Ensure all tests pass before submitting a pull request.
- Aim for good test coverage.

## Pull Request Process

1. Update the README.md with details of changes if needed.
2. Update the CHANGELOG.md with your changes.
3. The pull request will be reviewed by maintainers.
4. Make any requested changes.
5. Once approved, your changes will be merged.

## Commit Messages

- Use clear, descriptive commit messages.
- Start with a verb (Add, Fix, Update, etc.).
- Keep the first line under 50 characters.
- Provide more detail in the body if needed.

Example:
```
Add user authentication feature

- Implement login functionality
- Add password reset capability
- Update user model with authentication methods
```

## License

By contributing to Laravel Installer, you agree that your contributions will be licensed under the same license as the project.

## Questions?

If you have any questions about contributing, feel free to ask in the GitHub discussions or contact the maintainers.