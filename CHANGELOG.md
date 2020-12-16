# Changelog

All notable changes to `ImmutaFopen` will be documented in this file.

## 0.1.1 - 2020-12-15
- Fix composer email and license info.
- Update changelog stuff.

## 0.1.0 - 2020-12-15
- Add helper method to `ImmutaFopen` to create a new incremented method.  (Shortcut to `ImmutaFopen::recycleAtBytePosition(self $existingSocket, int $bytePosition)`)
- Update `__toString()` when file is empty to return empty string.
- Added a bunch of tests.

## 0.0.2 - 2020-12-15

- Add infection to testing stack.
- Remove ability to pass a file-handler.
- Remove unnecessary rewind action.
- Add `strict_types` and update return types.

## 0.0.1 - 2020-12-15

- initial release