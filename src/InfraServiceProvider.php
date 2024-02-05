<?php

namespace Laravel\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Event;
use Laravel\Infrastructure\AwsServices\AwsS3BucketService;
use Laravel\Infrastructure\AwsServices\AwsSNSService;
use Laravel\Infrastructure\Facades\FilterDataBuilderServiceFacade;
use Laravel\Infrastructure\Listeners\AuditingListener;
use Laravel\Infrastructure\Mail\EmailService;
use Laravel\Infrastructure\Mail\V2\EmailService as EmailServiceV2;
use Laravel\Infrastructure\Middlewares\ConvertToJson;
use Laravel\Infrastructure\Middlewares\StrictJsonNumericCheck;
use Laravel\Infrastructure\Middlewares\UniqueRequestId;
use Laravel\Infrastructure\Models\CustomAuditStateContainer;
use Laravel\Infrastructure\Repositories\SearchFilterConfigRepository;
use Laravel\Infrastructure\Services\ExceptionReporterService;
use Laravel\Infrastructure\Services\FilterDataBuilderService;
use Laravel\Infrastructure\Services\ModulrAuthCredService;
use Laravel\Infrastructure\Services\ImageProcessorService;
use Laravel\Infrastructure\Services\RequestSessionService;
use Laravel\Infrastructure\Services\SearchFilterService;
use OwenIt\Auditing\Events\Auditing;
use Laravel\Infrastructure\Services\PdfCreationService;
use Laravel\Infrastructure\Services\ExcelDownloadService;
use Laravel\Infrastructure\Services\FcmPushNotifiService;
use Laravel\Infrastructure\Services\OpenAiPoSuggestedMatchService;

class InfraServiceProvider extends ServiceProvider
{
    public function boot(Kernel $kernel)
    {
        $kernel->prependMiddlewareToGroup("api", ConvertToJson::class);
        $kernel->prependMiddlewareToGroup("api", UniqueRequestId::class);
        if ($this->app->runningInConsole()) {
            // $this->publishes([
            //     __DIR__ . '/Config/audit.php' => config_path('audit.php'),
            // ]);
            $this->publishes([
                __DIR__ . '/Config/webhook-client.php' => config_path('webhook-client.php'),
            ]);
        }
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/Config/modulr.php', "modulr");
        $this->mergeConfigFrom(__DIR__ . '/Config/microappservices.php', "microappservices");
        $this->mergeConfigFrom(__DIR__ . '/Config/awsS3Bucket.php', "awsS3Bucket");
        $this->mergeConfigFrom(__DIR__ . '/Config/app.php', "app");
        $this->mergeConfigFrom(__DIR__ . '/Config/audit.php', "audit");
        $this->mergeConfigFrom(__DIR__ . '/Config/queue.php', "queue");
        $this->loadViewsFrom(__DIR__ . '/Views', 'klooviews');

        Event::listen(
            Auditing::class,
            [AuditingListener::class, 'handle']
        );
    }

    public function register()
    {
        $this->app->singleton("requestsession", function ($app) {
            return new RequestSessionService();
        });

        $this->app->singleton("custom_audit_state_container", function ($app) {
            return new CustomAuditStateContainer();
        });

        $this->app->bind("emailservice", function () {
            return new EmailService();
        });

        $this->app->bind("awss3bucketservice", function () {
            return new AwsS3BucketService();
        });

        $this->app->singleton("exception_reporter_service", function () {
            return new ExceptionReporterService();
        });

        $this->app->bind("modulrauthcredservice", function () {
            return new ModulrAuthCredService();
        });

        $this->app->singleton("image_processor_service", function () {
            return new ImageProcessorService();
        });

        $this->app->singleton("search_filter_service", function () {
            return new SearchFilterService();
        });

        $this->app->singleton("search_filter_config_repo", function () {
            return new SearchFilterConfigRepository();
        });

        $this->app->singleton("filter_data_builder_service", function () {
            return new FilterDataBuilderService();
        });

        $this->app->singleton("pdf_creation_service", function () {
            return new PdfCreationService();
        });

        $this->app->singleton("search_filter_service", function () {
            return new SearchFilterService();
        });

        $this->app->singleton("excel_download_service", function () {
            return new ExcelDownloadService();
        });

        $this->app->singleton("fcm_push_notifi_service", function () {
            return new FcmPushNotifiService();
        });

        $this->app->singleton("aws_sns_service", function () {
            return new AwsSNSService();
        });

        $this->app->singleton("email_service_v2", function () {
            return new EmailServiceV2();
        });

        $this->app->bind("openAi_po_suggested_match_service", function () {
            return new OpenAiPoSuggestedMatchService();
        });
    }
}
