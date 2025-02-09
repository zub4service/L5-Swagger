<?php

use L5Swagger\Exceptions\L5SwaggerException;

if (! function_exists('swagger_ui_dist_path')) {
    /**
     * Returns swagger-ui composer dist path.
     *
     * @param  string  $documentation
     * @param  null  $asset
     * @return string
     *
     * @throws L5SwaggerException
     */
    function swagger_ui_dist_path(string $documentation, $asset = null)
    {
        $allowed_files = [
            'favicon-16x16.png',
            'favicon-32x32.png',
            'oauth2-redirect.html',
            'swagger-ui-bundle.js',
            'swagger-ui-bundle.js.map',
            'swagger-ui-standalone-preset.js',
            'swagger-ui-standalone-preset.js.map',
            'swagger-ui.css',
            'swagger-ui.css.map',
            'swagger-ui.js',
            'swagger-ui.js.map',
        ];

        $defaultPath = 'vendor/swagger-api/swagger-ui/dist/';
        $path = base_path(
            config('l5-swagger.documentations.'.$documentation.'.paths.swagger_ui_assets_path', $defaultPath)
        );

        if (! $asset) {
            return realpath($path);
        }

        if (! in_array($asset, $allowed_files)) {
            throw new L5SwaggerException(sprintf('(%s) - this L5 Swagger asset is not allowed', $asset));
        }

        return realpath($path.$asset);
    }
}

if (! function_exists('l5_swagger_asset')) {
    /**
     * Returns asset from swagger-ui composer package.
     *
     * @param  string  $documentation
     * @param  $asset  string
     * @return string
     *
     * @throws L5SwaggerException
     */
    function l5_swagger_asset(string $documentation, $asset)
    {
        $file = swagger_ui_dist_path($documentation, $asset);

        if (! file_exists($file)) {
            throw new L5SwaggerException(sprintf('Requested L5 Swagger asset file (%s) does not exists', $asset));
        }

        $useAbsolutePath = config('l5-swagger.documentations.'.$documentation.'.paths.use_absolute_path', true);

        return route('l5-swagger.'.$documentation.'.asset', $asset, $useAbsolutePath).'?v='.md5_file($file);
    }
}

if (! function_exists('overrideDocsPathByTenant')) {
    /**
     * Returns back configuration array with overrided documentation.paths.docs path if multitenant enabled.
     *
     * @param  array  $configuration
     * @return array
     */
    function overrideDocsPathByTenant(array $configuration)
    {
        if ($configuration['tenancy_for_laravel']) {

            $base_storage_path = base_path('storage') . DIRECTORY_SEPARATOR;
            $inside_storage_path = str_replace($base_storage_path, '', $configuration['paths']['docs']);
            $tenancy_storage_path = storage_path($inside_storage_path);

            // @override docs path by tenant
            $configuration['paths']['docs'] = storage_path($inside_storage_path);
            unset($base_storage_path);
            unset($inside_storage_path);
        }

        return $configuration;
    }
}
