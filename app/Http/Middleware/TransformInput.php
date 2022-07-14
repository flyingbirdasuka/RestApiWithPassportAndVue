<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    // for the validation with transformed names (when you use post to send data)
    public function handle(Request $request, Closure $next, $transformer)
    {
        $transformedInput = [];
        // not the query string in the url
        foreach($request->request->all() as $input => $value){
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }
        $request->replace($transformedInput);

        $response = $next($request);
        
        if($response->exception && $response->exception instanceof ValidationException){
            $data = $response->getData();
            $transformedErrors = [];
            foreach($data->error as $field => $error){
                $transformedField = $transformer::transformedAttribute($field); 
                $transformedErrors[$transformedField] = str_replace($field,$transformedField, $error); 
            }
            $data->error = $transformedErrors;
            $response->setData($data);
        }
        return $response;
    }
}
