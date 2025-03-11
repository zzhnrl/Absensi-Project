<?php
// phpcs:ignoreFile
namespace App\Services;

use App\Services\ServiceInterface;
use App\Traits\Audit;
use App\Traits\Identifier;
use App\Traits\Pagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class DefaultService implements ServiceInterface
{
    use Audit;
    use Identifier;
    use Pagination;

    protected $results;

    abstract protected function process($data);
    public function rules ($data) { return []; }
    public function messages ($data) { return []; }

    public function execute($inputData, $subService = false)
    {
        $this->results = ['response_code' => 200, 'error' => null, 'message' => null, 'data' => null];

        if (!$subService) {
            $timeStart = microtime(true);
            DB::beginTransaction();
            try {
                $validator = Validator::make($inputData, $this->rules($inputData), $this->messages($inputData));
                if ($validator->fails()) {
                    throw new HttpResponseException(response()->json($validator->errors(), 422));
                }

                $this->process($inputData);
                DB::commit();
            } catch (HttpResponseException $ex ) {
                DB::rollback();
                $this->results['response_code'] = 422;
                $this->results['error'] = $ex;
                $this->results['message'] = $ex->getResponse()->original;
            } catch (\Exception $ex) {
                DB::rollback();
                $this->results['response_code'] = $ex->getCode() == 0 ? 500 : $ex->getCode() ;
                $this->results['error'] = $ex;
                if (request()->ip() == '127.0.0.1') {
                    $this->results['message'] = 'Caught exception: "' . $ex->getMessage() . '" on line ' . $ex->getLine() . ' of ' . $ex->getFile();
                } else {
                    $this->results['message'] = $ex->getMessage();
                }
            }

            return $this->results;
        } else {
            $this->process($inputData);
            return $this->results;
        }
    }
}
