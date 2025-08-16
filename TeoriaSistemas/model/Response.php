<?php
class Response
{
    static function success($mensaje, $data = null)
    {
        $valor = [
            "status" => "bien",
            "mensaje" => $mensaje,
            "data" => $data
        ];
        print_r(json_encode($valor));
    }

    static function error($mensaje)
    {
        $valor = [
            "status" => "error",
            "mensaje" => $mensaje,
        ];
        print_r(json_encode($valor));
    }
}
