<?php

function isProduction() {
    return config('app.env') == 'production';
}

function isStaging() {
    return config('app.env') == 'staging';
}

function isLocal() {
    return config('app.env') == 'local';
}

function isTesting() {
    return config('app.env') == 'testing';
}
