<?php

namespace App\Http\Middleware;

use Closure;

class ETag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get response
        $response = $next($request);
        if ($request->isMethod('GET')) {
            // Generate ETag
            $etag = md5($response->getContent());
            $requestEtag = str_replace('"', '', $request->getETags());
            // Check to see if ETag has changed
            if ($requestEtag && $requestEtag[0] == $etag) {
                $response->setNotModified();
            }
            // Set Etag
            $response->setEtag($etag);
        }
        // Send response
        return $response;
    }
}
