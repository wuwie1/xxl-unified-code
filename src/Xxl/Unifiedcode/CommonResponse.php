<?php


namespace Xxl\Unifiedcode;

use Xxl\Unifiedcode\Exceptions\NonfatalException;

/**
 * Trait CommonResponse
 * @desc 目前平台响应里通用的方法集
 */
trait CommonResponse
{
    /**
     * @desc 请求需要返回成功信息时
     * @param array $data 成功是附带的数据列表
     * @param int $code 成功返回的状态码
     * @param array $ext_data 需要附加带过去的额外的数据列表
     * @param array $params 语言文件需要的参数
     * @return \Illuminate\Http\JsonResponse
     */
    public function onSuccess($data = [], int $code = 0, array $ext_data = [], array $params = [])
    {
        $message = $this->getResponseMessageByCode($code, $params);
        $response_data = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
        if (!empty($ext_data)) {
            $response_data['ext_info'] = $ext_data;
        }
        return response()->json($response_data);
    }

    /**
     * @param int $code
     * @param array $ext_data
     * @param array $params
     * @return mixed
     */
    public function onError(int $code, array $ext_data = [], array $params = [])
    {
        $message = $this->config->get('app.name') . ':' . ($this->getResponseMessageByCode($code, $params));
        $response_data = [
            'code' => $code,
            'message' => $message,
        ];
        if (!empty($ext_data)) {
            $response_data['ext_info'] = $ext_data;
        }
        return response()->json($response_data);
    }

    /**
     * @param NonfatalException $e
     * @return mixed
     */
    public function nonfatalError(NonfatalException $e)
    {
        $message = $this->config->get('app.name') . ':' . ($e->getMessage());
        $response_data = [
            'code' => $e->getCode(),
            'message' => $message,
        ];
        if (!empty($ext_data)) {
            $response_data['ext_info'] = $ext_data;
        }
        return response()->json($response_data);
    }

    /**
     * @param int $code
     * @param array $params
     * @throws NonfatalException
     */
    public function nonfatalException(int $code, $params = [])
    {
        $mess = $this->getResponseMessageByCode($code, $params);
        throw new NonfatalException($code, $mess);
    }

    /**
     * @param int $code 通过给定的状态码获取语言包中的错误信息
     * @param array $params 语言文件需要的参数
     * @return mixed|string
     */
    public function getResponseMessageByCode($code, array $params)
    {
        require_once(__DIR__ . '/config/ConfigCode.php');
        $msg = isset($common[$code]) ? $common[$code] : trans($this->config->get('app.name') . '.' . $code, $params);
        $keys = array_keys($params);
        foreach ($keys as $k => &$key) {
            $key = ':' . $key;
        }
        $msg = $params ? str_replace($keys, array_values($params), $msg) : $msg;
        return $msg;
    }

    /**
     * @desc 请求需要返回成功信息时
     * @param array $data 成功是附带的数据列表
     * @param int $code 成功返回的状态码
     * @return \Illuminate\Http\JsonResponse
     */
    public function onSuccessV2(array $data = [], int $code = 0)
    {
        $response_data = [
            'code' => $code,
            'message' => 'success',
            'data' => $data,
        ];
        return response()->json($response_data);
    }

    /**
     * @param int $code 返回的错误码
     * @param string $msg 错误提示信息
     * @param array $params 需要替换的错误提示信息中的参数
     * @return mixed
     */
    public function onErrorV2(int $code, string $msg = '', array $params = [])
    {
        if ($msg) {

            $msg = empty($params) ? $msg : str_replace(array_keys($params), array_values($params), $msg);
        } else {

            $msg = $this->config->get('app.name') . ':' . ($this->getResponseMessageByCode($code, []));
        }

        $response_data = [
            'code' => $code,
            'message' => $msg,
        ];

        return response()->json($response_data);
    }
}
