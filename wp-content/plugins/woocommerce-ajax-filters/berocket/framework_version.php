<?php
$framework_version_current = '2.9.1';
if( version_compare($framework_version_current, $framework_version, '>') ) {
    $framework_version = $framework_version_current;
    $framework_dir = __DIR__;
}
