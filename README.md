# Domain

Master:
[![Build Status](https://travis-ci.org/t4web/Domain.svg?branch=master)](https://travis-ci.org/t4web/Domain)
[![codecov.io](http://codecov.io/github/t4web/Domain/coverage.svg?branch=master)](http://codecov.io/github/t4web/Domain?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/t4web/Domain/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/t4web/Domain/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4405512a-da0f-415c-97cf-b8d4ef5f9d43/mini.png)](https://insight.sensiolabs.com/projects/4405512a-da0f-415c-97cf-b8d4ef5f9d43)
[![Dependency Status](https://www.versioneye.com/user/projects/563887a1e93564001a000200/badge.svg?style=flat)](https://www.versioneye.com/user/projects/563887a1e93564001a000200)

Domain Driven Design implementation by [t4web\domain-interface](https://github.com/t4web/DomainInterface)

## Contents
- [Installation](#instalation)
- [Quick start](#quick-start)

## Installation

Add this project in your composer.json:

```json
"require": {
    "t4web/domain": "~1.0.0"
}
```

Now tell composer to download Domain by running the command:

```bash
$ php composer.phar update
```

## Quick start

You can use service `Creator`, `Deleter` and `Updater` in you controllers
for work with DDD Entity. You can implement `Domain\Infrastructure` for using
this services or inject [t4web\infrastructure](https://github.com/t4web/Infrastructure)