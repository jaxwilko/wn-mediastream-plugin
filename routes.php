<?php

Route::get('/mediastream/{video}', 'JaxWilko\MediaStream\Classes\Stream@make')->name('mediaStream');
