# Analytics

This is a small class to post Analytics events from PHP. This is useful for logging and event tracking.

```php
<?php

use Deimos\Analytics\Analytics;

(new Analytics('UAxxxxxxx', 'project.deimos'))
    ->setResource('image.png')
    ->setAction('download')
    ->setLabel('nginx')
    ->track();
```