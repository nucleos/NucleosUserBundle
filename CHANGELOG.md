# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.11.3 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.11.2 - 2021-09-15


-----

### Release Notes for [1.11.2](https://github.com/nucleos/NucleosUserBundle/milestone/11)

1.11.x bugfix release (patch)

### 1.11.2

- Total issues resolved: **0**
- Total pull requests resolved: **1**
- Total contributors: **1**

#### Bug

 - [433: Fix html for resetting mail](https://github.com/nucleos/NucleosUserBundle/pull/433) thanks to @core23

## 1.11.1 - 2021-08-04


-----

### Release Notes for [1.11.1](https://github.com/nucleos/NucleosUserBundle/milestone/9)

1.11.x bugfix release (patch)

### 1.11.1

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **2**

 - [398: Remove test deprecations](https://github.com/nucleos/NucleosUserBundle/pull/398) thanks to @core23

#### Bug

 - [397: PatternValidator ignores null values and does not throw an exception anymore](https://github.com/nucleos/NucleosUserBundle/pull/397) thanks to @AubreyHewes

## 1.11.0 - 2021-07-02


-----

### Release Notes for [1.11.0](https://github.com/nucleos/NucleosUserBundle/milestone/8)

Feature release (minor)

### 1.11.0

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **2**

#### Enhancement

 - [376: Add support for html mails](https://github.com/nucleos/NucleosUserBundle/pull/376) thanks to @core23
 - [372: add Russian translate](https://github.com/nucleos/NucleosUserBundle/pull/372) thanks to @a1812

## 1.10.0 - 2021-06-12


-----

### Release Notes for [1.10.0](https://github.com/nucleos/NucleosUserBundle/milestone/6)

Feature release (minor)

### 1.10.0

- Total issues resolved: **0**
- Total pull requests resolved: **5**
- Total contributors: **2**

#### Bug

 - [363: Fix showing form errors twice](https://github.com/nucleos/NucleosUserBundle/pull/363) thanks to @core23
 - [361: Missing translation of login errors](https://github.com/nucleos/NucleosUserBundle/pull/361) thanks to @core23

#### Enhancement

 - [362: Use default form theme](https://github.com/nucleos/NucleosUserBundle/pull/362) thanks to @core23
 - [359: Force string typecast when reading username from request](https://github.com/nucleos/NucleosUserBundle/pull/359) thanks to @core23
 - [302: Declare translation&#95;domain in configureOptions.](https://github.com/nucleos/NucleosUserBundle/pull/302) thanks to @alexsegura

## 1.9.0 - 2021-03-16


-----

### Release Notes for [1.9.0](https://github.com/nucleos/NucleosUserBundle/milestone/3)

Feature release (minor)

### 1.9.0

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **1**

#### Bug

 - [292: Show form errors](https://github.com/nucleos/NucleosUserBundle/pull/292) thanks to @core23

#### Enhancement

 - [291: Add account deletion](https://github.com/nucleos/NucleosUserBundle/pull/291) thanks to @core23

## 1.8.0 - 2021-03-01



-----

### Release Notes for [1.8.0](https://github.com/nucleos/NucleosUserBundle/milestone/1)



### 1.8.0

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **1**

#### Bug

 - [273: Fix registering user checker](https://github.com/nucleos/NucleosUserBundle/pull/273) thanks to @core23

#### Enhancement

 - [272: Add pattern validator](https://github.com/nucleos/NucleosUserBundle/pull/272) thanks to @core23

## 1.7.1

### Changes

### 🐛 Bug Fixes

- Fixing typo in routing [@KhorneHoly] ([#227])

## 1.7.0

### Changes

- Make `FormEvent` non-final [@fkrauthan] ([#200])
- Move buttons to action [@core23] ([#207])

### 🐛 Bug Fixes

- Fix login error [@fkrauthan] ([#190])
- Fix passing error to login form [@core23] ([#182])

## 1.6.1

### Changes

### 🐛 Bug Fixes

- Fix button translation [@core23] ([#180])

## 1.6.0

### Changes

- Move submit buttons to form definition [@core23] ([#164])

### 🚀 Features

- Replace hardcoded forms with symfony forms [@core23] ([#167])

### 🐛 Bug Fixes

- Fix `PreUpdateEventArgs` import for mongodb [@core23] ([#176])
- Fix loading driver related validations [@core23] ([#173])

### 📦 Dependencies

- Add support for PHP 8 [@core23] ([#148])

## 1.5.0

### Changes

### 🐛 Bug Fixes

- Add more specific phpdoc [@core23] ([#120])
- Add more specific phpdoc [@core23] ([#141])

### 📦 Dependencies

- Add doctrine/common 3 support [@TorbenLundsgaard] ([#137])
- Drop doctrine/mongodb-odm 1 [@TorbenLundsgaard] ([#136])

## 1.4.0

### Changes

### 🚀 Features

- Adding Spanish translations [@anacona16] ([#100])
- Move configuration to PHP [@core23] ([#52])

### 🐛 Bug Fixes

- Add setPlainPassword() before update user [@core23] ([#92])
- Remove deprecated mongo safe option [@core23] ([#50])
- Catch possible null error [@core23] ([#49])

## 1.3.1

### 🐛 Bug Fixes

- Disable setting twig timezone [@core23] ([#40])

## 1.3.0

### Changes

### 🐛 Bug Fixes

- Find methods could return null [@core23] ([#38])

## 1.2.0

### Changes

### 🚀 Features

- Use bootstrap 3 layout as default form theme [@core23] ([#36])

## 1.1.0

### Changes

### 🚀 Features

- Add noop driver to allow flex recipe [@core23] ([#34])
- Add missing toString method to Group model [@core23] ([#32])
- Make service aliases public [@core23] ([#30])

### 🐛 Bug Fixes

- Prefix generics with [@phpstan] [@core23] ([#31])

[#141]: https://github.com/nucleos/NucleosUserBundle/pull/141
[#137]: https://github.com/nucleos/NucleosUserBundle/pull/137
[#136]: https://github.com/nucleos/NucleosUserBundle/pull/136
[#120]: https://github.com/nucleos/NucleosUserBundle/pull/120
[#100]: https://github.com/nucleos/NucleosUserBundle/pull/100
[#92]: https://github.com/nucleos/NucleosUserBundle/pull/92
[#52]: https://github.com/nucleos/NucleosUserBundle/pull/52
[#50]: https://github.com/nucleos/NucleosUserBundle/pull/50
[#49]: https://github.com/nucleos/NucleosUserBundle/pull/49
[#40]: https://github.com/nucleos/NucleosUserBundle/pull/40
[#38]: https://github.com/nucleos/NucleosUserBundle/pull/38
[#36]: https://github.com/nucleos/NucleosUserBundle/pull/36
[#34]: https://github.com/nucleos/NucleosUserBundle/pull/34
[#32]: https://github.com/nucleos/NucleosUserBundle/pull/32
[#31]: https://github.com/nucleos/NucleosUserBundle/pull/31
[#30]: https://github.com/nucleos/NucleosUserBundle/pull/30
[@phpstan]: https://github.com/phpstan
[@core23]: https://github.com/core23
[@anacona16]: https://github.com/anacona16
[@TorbenLundsgaard]: https://github.com/TorbenLundsgaard
[#176]: https://github.com/nucleos/NucleosUserBundle/pull/176
[#173]: https://github.com/nucleos/NucleosUserBundle/pull/173
[#167]: https://github.com/nucleos/NucleosUserBundle/pull/167
[#164]: https://github.com/nucleos/NucleosUserBundle/pull/164
[#148]: https://github.com/nucleos/NucleosUserBundle/pull/148
[#180]: https://github.com/nucleos/NucleosUserBundle/pull/180
[#207]: https://github.com/nucleos/NucleosUserBundle/pull/207
[#200]: https://github.com/nucleos/NucleosUserBundle/pull/200
[#190]: https://github.com/nucleos/NucleosUserBundle/pull/190
[#182]: https://github.com/nucleos/NucleosUserBundle/pull/182
[@fkrauthan]: https://github.com/fkrauthan
[#227]: https://github.com/nucleos/NucleosUserBundle/pull/227
[@KhorneHoly]: https://github.com/KhorneHoly
