<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

foreach ($assets ?? [] as $asset) {
    echo sprintf('<link rel="stylesheet" type="text/css" href="%s">', $asset);
}
