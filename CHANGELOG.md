# Changelog

All notable changes to `ImmutableFile` will be documented in this file.

## 0.5.1 - 2020-12-18
- Remove traces of old package name.

## 0.5.0 - 2020-12-17
- Add a FileHandlerManager to share file handles between common ImmutableFile entities.
- Add tests to cover new class above.
- Made it so multiple instances to the same file share a common WeakRef file socket.

## 0.4.0 - 2020-12-17
- Refactored the names of the project and underlying classes.

## 0.3.0 - 2020-12-15
- Added `fgets` and `eof` + `feof` methods.

## 0.2.0 - 2020-12-15
- Add new `ImmutaFopen::fromFilePathWithPosition` method.

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