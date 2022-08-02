## Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.1.0 alpha 12] 2020-01-01

### Added

- Added name property to criterias
- Support for Custom Fields

## [0.1.0 alpha 11] 2019-05-11

### Added

- Profile fields now are visible in component backend user listing

### Fixed

- Fixed error on install about missing language file for package
- Fixed user activation

### Changed

- Updated footables plugin
- User activation and block now share edit.state permissions

## [0.1.0 alpha 10] 2019-03-13

### Added

- Added Site Language and timezone to the list of fields in the backend
- Added Profile fields filter for list view
- Missing language strings

### Fixed

- Fixed removing users in criteria removing it from all fields
- Fixed usability of multiple users select in criteria

## [0.1.0 alpha 9]

### Added

- Simple Export of users in the frontend

### Fixed

- Showing previous criteria properties in the criterias listing
- Message when duplciating criteria
- Changing criteria state

## [0.1.0 alpha 8]

### Added

- Added possibility to exclude users form listings in the criteria

### Fixed

- Issues with search and pagination in modal listing of users to select a user
- User list area now shows users

## [0.1.0 alpha 7]

### Fixed

- Fixed query when searching by field which "contains" some text

## [0.1.0 alpha 6]

### Added

- Added Profile Fields greater than and less than in the Criteria filters
- Full support for Excluded Fields in Criteria area
- Select all button for ProfileFields when multiple values allowed

## [0.1.0 alpha 5]

### Added

- Permissions tab on criteria 
- Actions can be defined by criteria

### Fixed

- Value format in multipleusers field

## [0.1.0 alpha 4]

### Added

- Hide fields in edit if not shown in the menu item
- Actions buttons hidden when not enough permissions

### Fixed

- Name, username and email filter in main search box

## [0.1.0 alpha 3]

### Added

- User Edit in frontend
- Criteria options in backend to define specific manager capabilities
- Filtering by any profile field
- Option to exclude fields from listing
- Option to exclude fields from filters
- Single UserGroup filter

### Changed

- Refactored helpers autoload to current coding standards

#### [0.0.6] - 2017-01-10

###### Added

- Show package description after installation

###### Changed

- Updated hepta form fields library to latest code

###### Fixed

- Remove testing filter backup and testing filter form the package
- Fixed date ranges in register and last visit filters not to search in the future dates
- Removed 1 week date constrain in the register and last visit filters

#### [0.0.5] - 2017-01-10 (Thanks [Brian](http://brian.teeman.net)!!)

###### Fixed

- Fixing previous package version and install script

#### [0.0.4] - 2017-01-03 (Thanks [Brian](http://brian.teeman.net)!!)

###### Fixed

- Removed testing filter from the package
- Fixed non-defined variables in the layouts

#### [0.0.3] - 2016-10-14

###### Added

- Now you can set which groups should be shown in the view settings.

###### Removed

- Double permission check. Now you only need Frontend User Manager permissions to see the lists

#### [0.0.2] - 2016-09-02

###### Added

- hepta.customdaterange field from Hepta Form Fields Library for easy Custom Date Range selection
- filter_joomlacore.xml form to allow easy filtering of Joomla! Base Fields
- System will read any Joomla! xml form description added into the model and will integrate their fields in the search form
- Allow excluding groups in the listing
- Enabling Live update stream for all users. This will change in the future

###### Changed

- Double the security and user should have permissions to manage Joomla Users component and Frontend User Manager Component

#### [0.0.1] - 2016-05-20

###### Added

- Initial Release

###### Changed

- Initial Release

###### Fixed

- Initial Release
