# Resize animated Gif files


This package aims to implement a proper resizing of gif files encompassing the GIF89a specification.
 

## Introduction

Most, if not all other publicly available gif resize packages fails with optimized gif files,
those where only parts of the file are updated in subsequent frames. See these as a background image,
with sprites moving about.
The resulting gif will retain its aspect ratio.

The package is a bit pedantic in its approach. It was made as much for me to learn what Gifs were 
and how they work, as it was to solve specific problem.

## Usage

The package needs to write to a file, the reasons for not just return a string is twofold.
One being memory usage, the other is that you really don't want to be dynamically resizing
often used gif files every time they are used.

### Import
Add this requirement to your `composer.json` file:
```json
"grandt/phpresizegif": ">=1.0.1"
```

### Initialization

```php
include "../vendor/autoload.php";
use grandt\ResizeGif\ResizeGif;

$srcFile = "[path to original gif file]";
$dstFile = "[path to resized file]";

ResizeGif::ResizeToWidth($srcFile, $dstFile, 100);
```

### To make a 100 pixel wide thumbnail

```php
ResizeGif::ResizeToWidth($srcFile, $dstFile, 100);
```

### To make a 100 pixel high thumbnail

```php
ResizeGif::ResizeToHeight($srcFile, $dstFile, 100);
```

### To double the size of the gif.

```php
ResizeGif::ResizeByRatio($srcFile, $dstFile, 2.0);
```

### To half the size of the gif.

```php
ResizeGif::ResizeByRatio($srcFile, $dstFile, 0.5);
```
