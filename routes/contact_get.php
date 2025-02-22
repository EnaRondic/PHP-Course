<?php
var_dump($_GET);
renderView('contact_get', ['csrfToken' => getCurrentCsrfToken()]);