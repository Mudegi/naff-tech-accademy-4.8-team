<?php
file_put_contents(dirname(__FILE__) . '/../storage/logs/test.log', date('Y-m-d H:i:s') . " - Test request received\n", FILE_APPEND);
echo 'Test route works!';
?>