# 1.6.0 

## Changes

- Move submit buttons to form definition @core23 (#164)

## 🚀 Features

- Replace hardcoded forms with symfony forms @core23 (#167)

## 🐛 Bug Fixes

- Fix `PreUpdateEventArgs` import for mongodb @core23 (#176)
- Fix loading driver related validations @core23 (#173)

## 📦 Dependencies

- Add support for PHP 8 @core23 (#148)

# 1.5.0

## Changes

## 🐛 Bug Fixes

- Add more specific phpdoc [@core23] ([#120])
- Add more specific phpdoc [@core23] ([#141])

## 📦 Dependencies

- Add doctrine/common 3 support [@TorbenLundsgaard] ([#137])
- Drop doctrine/mongodb-odm 1 [@TorbenLundsgaard] ([#136])

# 1.4.0

## Changes

## 🚀 Features

- Adding Spanish translations [@anacona16] ([#100])
- Move configuration to PHP [@core23] ([#52])

## 🐛 Bug Fixes

- Add setPlainPassword() before update user [@core23] ([#92])
- Remove deprecated mongo safe option [@core23] ([#50])
- Catch possible null error [@core23] ([#49])

# 1.3.1

## 🐛 Bug Fixes

- Disable setting twig timezone [@core23] ([#40])

# 1.3.0

## Changes

## 🐛 Bug Fixes

- Find methods could return null [@core23] ([#38])

# 1.2.0

## Changes

## 🚀 Features

- Use bootstrap 3 layout as default form theme [@core23] ([#36])

# 1.1.0

## Changes

## 🚀 Features

- Add noop driver to allow flex recipe [@core23] ([#34])
- Add missing toString method to Group model [@core23] ([#32])
- Make service aliases public [@core23] ([#30])

## 🐛 Bug Fixes

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
