<?php

namespace App\Listeners;

use Illuminate\Http\Request;
use Illuminate\Database\Events\QueryExecuted;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;

class DBQueryExecutedListener
{
    /**
     * Current Request
     * 
     * @var Request;
     */
    protected $request;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        \Log::channel('query')->info(
            json_encode(
                [
                    "type" => "QueryLog",
                    "url" => $this->request->url(),
                    "hostname" => gethostname(),
                    "query" => $this->getSqlWithBindings($event),
                    "executionTime" => $event->time,
                    'timestamp' => \Carbon\Carbon::now()->timestamp
                ]
            )
        );
    }
    
    /**
     * Returns sql query with bindigs.
     *
     * @param  QueryExecuted  $event
     * @return string
     */
    protected function getSqlWithBindings(QueryExecuted $event)
    {
    
        $sql = $event->sql;
        foreach($event->bindings as $binding)
        {
          if(get_debug_type($binding) == "DateTime"){
            $value = $binding->format('Y-m-d H:i:s');
              
          }else{
         
          $value = is_numeric($binding) ? $binding : "'".$binding."'";
          }
          $sql = preg_replace('/\?/', $value, $sql, 1);
         
        }

        return $sql;
    
    }
}
