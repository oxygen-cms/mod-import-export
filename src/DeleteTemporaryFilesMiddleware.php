<?php


namespace OxygenModule\ImportExport;

use Closure;
use Illuminate\Contracts\Routing\TerminableMiddleware;
use Illuminate\Foundation\Application;

class DeleteTemporaryFilesMiddleware implements TerminableMiddleware {

    /**
     * @var \Illuminate\Foundation\Application
     */
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        return $next($request);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function terminate($request, $response) {
        foreach($this->app[ImportExportManager::class]->temporaryFilesToDelete as $file) {
            unlink($file);
        }
    }

}