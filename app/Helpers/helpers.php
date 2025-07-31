<?php
// app/helpers.php

if (!function_exists('prefixed_route')) {
    function prefixed_route($name, $parameters = [], $absolute = true)
    {
        $prefix = config('app.company_prefix');

        if ($prefix) {
            // Se não é array, transformar
            if (!is_array($parameters)) {
                $parameters = $parameters ? [$parameters] : [];
            }

            // Adicionar o prefixo
            $parameters = array_merge(['company_prefix' => $prefix], $parameters);
        }

        return route($name, $parameters, $absolute);
    }
}
