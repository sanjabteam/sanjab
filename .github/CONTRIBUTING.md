# Contributing

I'm really glad you're reading this, thanks for taking the time to contribute!
- [Code of Conduct](#code-of-conduct)
- [Bug Report](#bug-report)
- [Suggesting Enhancements](#suggesting-enhancements)
- [Documentation Contribution](#documentation-contribution)
- [Code Contribution](#code-contribution)
  - [Getting started](#getting-started)
  - [Testing](#testing)
  - [Code Style](#code-style)
  - [Coding conventions](#coding-conventions)
  - [Questions about source code](#questions-about-source-code)
  - [Making Pull Request](#making-pull-request)

## Code of Conduct
Read [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md) for more information.

## Bug Report

* Do NOT open an issue if you found security bug/vulnerability. [read this](./SECURITY.md).
* Make sure the bug was not already reported by searching on [Issues](https://github.com/sanjabteam/sanjab/issues).
* If you didn't find any open issue about your problem, [create new one](https://github.com/sanjabteam/sanjab/issues/new). Make sure to include an informative description of the issue.

## Suggesting Enhancements
* To suggest new feature for sanjab open new [Issue](https://github.com/sanjabteam/sanjab/issues/new).


## Documentation Contribution
Fork https://github.com/sanjabteam/docs and use [vuepress](https://vuepress.vuejs.org) to show results locally and then create a pull request.
Also, don't change vuepress configs and just edit markdown files.


## Code Contribution

### Getting started
1. First, create an empty laravel project.
```bash
composer create-project --prefer-dist laravel/laravel sanjab-project
```
2. Go to your new project directory. `cd sanjab-project`.
3. Fork [Sanjab](https://github.com/sanjabteam/sanjab) on GitHub.
4. Clone your forked repository.
```bash
git clone https://github.com/YOUR_USERNAME/sanjab.git
```
5. Open your project `composer.json` and add a new [composer path repository](https://getcomposer.org/doc/05-repositories.md).
```json
"repositories": [
    {
        "type": "path",
        "url": "./sanjab"
    }
]
```
6. Install sanjab from the local path.
```bash
composer require sanjabteam/sanjab dev-master
```
7. Make sure you installed sanjab from the local path, not packagist.
```bash
Installing sanjabteam/sanjab (dev-master): Junctioning from ./sanjab
```
8. Install sanjab.
```bash
php artisan sanjab:install
```
9. Fill `.env` database connection config and then run migrations.
10. Go to `public/vendor` and remove the `sanjab` directory.
11. Make a symbolic link for the sanjab directory instead.
```bash
php -r "symlink(realpath(__DIR__.'/../../vendor/sanjabteam/sanjab/resources/assets'), 'sanjab');"
```
> Or use `ln` on Linux / `mklink` on Windows instead.
12. Open website and make sure asset files loading successfully and symbolic link working.
```
http://yousite.test/admin
```
13. Now get back to the root and to sanjab directory itself.
```bash
cd ../../sanjab
```
14. **Important Step:** Set [git hooks](https://git-scm.com/docs/githooks) path.
```bash
git config core.hooksPath .githooks
```
15. Create a new git branch.
16. Install composer and npm packages.
```bash
composer install
npm install
```
17. Start compiling.
```bash
npm run watch
```
18. Hooray! you are ready to contribute sanjab.

### Testing
All tests are based on [PHPUnit](https://github.com/sebastianbergmann/phpunit). make sure your code does not break sanjab functionality.

Also, you should create new tests for your new features to make sure everything works.

To run tests use command `composer test`.

> Install composer packages before running tests.

### Code Style
Sanjab follows [PSR2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard. Please use [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) to test your code. Also, Sanjab using StyleCI to fix code styling issues on merge so don't worry a lot about it.

### Coding conventions

* Write clean and understandable code. [Read this](https://gist.github.com/wojteklu/73c6914cc446146b8b533c0988cf8d29).
* Never put compiled assets for development in git commits. Highly recommended to use githooks to prevent this happen.
* Keep your commits as small as possible and use informative git commit messages.
* Do not touch version in `package.json`.
* Class phpdoc definition and description must be in a column like a table.

❌ Bad:
```php
/**
 * @param string $var description about var.
 * @param string $anotherVar description about another var.
 * @method int aMethod (string $value) description about method.
 * @method float anotherMethod (string $value) description about another method.
 * @method $this anotherMoreMethod (string $value) description about another more method.
 */
```

 ✅ Good:
 ```php
 /**
 * @param string $var                               description about var.
 * @param string $anotherVar                        description about another var.
 * @method int aMethod (string $value)              description about method.
 * @method float anotherMethod (string $value)      description about another method.
 * @method $this anotherMoreMethod (string $value)  description about another more method.
 */
 ```

### Questions about source code
If you have any questions about Sanjab source code join us on [Discord](https://discord.gg/kwuTZQd).

### Making Pull Request
First, make sure your git branch is up to date with the Sanjab repository.
```bash
git remote add upstream https://github.com/sanjabteam/sanjab.git
git pull upstream master
```
Fix any possible conflict.

Push your changes to your repository.
```bash
git push origin YOUR_NEW_BRANCH -u
```
Go to GitHub and make a [pull request](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request) and Make sure your code passing Travis-CI tests.

Thank you again and welcome to sanjab contributors community.
