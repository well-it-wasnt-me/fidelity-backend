<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

foreach ($assets ?? [] as $asset) {
    echo sprintf('<script type="text/javascript" src="%s"></script>', $asset);
}
