<?php
/**
 * Created by IntelliJ IDEA.
 * User: memeo
 * Date: 31/01/19
 * Time: 03:29 AM
 */
class Api extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_models');
        $this->load->helper('url');
        $this->load->helper('text');
    }
    public function cars()
    {
        header("Access-Control-Allow-Origin: *");
        $res = $this->api_models->get_all(0);
        $carList = array();
        date_default_timezone_set('America/Caracas');

        if (!empty($res)) {
            foreach ($res as $item) {

                $carList[] = array(
                    'id_table' => (int)$item->id_table,
                    'empty' => (boolean)$item->empty,
                    'id' => $item->id,
                    'model' => $item->model,
                    'position' => (int)$item->position,
                    'dateIngress' =>$item->dateIngress,
                    'departureDate' => $item->departureDate,
                    'time' => (int)$item->time,
                    'numberAccess' => (int)$item->numberAccess,
                    'positionPrevious' => (int)$item->positionPrevious
                );
            }
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($carList));
    }


    public function generate_positions($cant)
    {
        header("Access-Control-Allow-Origin: *");
        for ( $i = 0 ; $i < $cant; $i++) {
            $data = array(
                'id_table' => $i,
                'empty' => true,
                'id' => '',
                'model' => '',
                'position' => -1,
                'dateIngress' => null,
                'departureDate' => null,
                'time' => 0,
                'numberAccess' => 0,
                'positionPrevious' => -1
            );
            $id = $this->api_models->insert_position($data);
        }
        $result = $id !== null ? true : false;
        $statusHeader = $id !== null ? 200 : 500;
        $this->output
            ->set_status_header($statusHeader)
            ->set_content_type('application/json')
            ->set_output($result);
    }


    function create_position_ramdom()
    {
        $res = $this->api_models->get_all(1);
        $positionList = array();
        if (!empty($res)) {
            foreach ($res as $item) {
                $positionList[] = (int)$item->id_table;
            }
        }
        $randomPosition = rand(0,sizeof($positionList)-1);
        if ($randomPosition % 2 === 0 and in_array($randomPosition+1,  $positionList)){
            return $positionList[$randomPosition + 1];
        }else {
            return $positionList[$randomPosition];
        }
    }

    public function register_position()
    {
        header("Access-Control-Allow-Origin: *");
        date_default_timezone_set('America/Caracas');
        $id=$this->input->post('id');
        $isEmpty = $this->api_models->get_position_by_licence_plate($id);
        if (empty($isEmpty)){
            $randomPosition = $this->create_position_ramdom();
            $this->api_models->update_position_register($randomPosition);
            $this->output
                ->set_content_type('application/json')
                ->set_output(true);
        } else {
            $res_ = array("result"  => false );
            $this->output
                ->set_content_type('application/json')
                ->set_output(false);
        }
    }


    public function get_five() {
        header("Access-Control-Allow-Origin: *");
        $res = $this->api_models->get_first_five();
        $carList = array();
        date_default_timezone_set('America/Caracas');
        if (!empty($res)) {
            foreach ($res as $item) {
                $time = strtotime($item->dateIngress);
                $date = ($time === false) ? '0000-00-00 00:00:00' : date('Y-m-d H:i:s', $time);
                $carList[] = array(
                    'id_table' => (int)$item->id_table,
                    'empty' => (boolean)$item->empty,
                    'id' => $item->id,
                    'model' => $item->model,
                    'position' => (int)$item->position,
                    'dateIngress' =>$date,
                    'departureDate' => $item->departureDate,
                    'time' => (int)$item->time,
                    'numberAccess' => (int)$item->numberAccess,
                    'positionPrevious' => (int)$item->positionPrevious
                );
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($carList));

    }

    public function get_by_licence_plate($id){
        header("Access-Control-Allow-Origin: *");
        date_default_timezone_set('America/Caracas');
        $res = $this->api_models->get_position_by_licence_plate($id);
        if (!empty($res)){
            foreach ($res as $item){
                $time = strtotime($item->dateIngress);
                $date = ($time === false) ? '0000-00-00 00:00:00' : date('Y-m-d H:i:s', $time);
                $result = array(
                    'id_table' => (int)$item->id_table,
                    'empty' => (boolean)$item->empty,
                    'id' => $item->id,
                    'model' => $item->model,
                    'position' => (int)$item->position,
                    'dateIngress' =>$date,
                    'departureDate' => $item->departureDate,
                    'time' => (int)$item->time,
                    'numberAccess' => (int)$item->numberAccess,
                    'positionPrevious' => (int)$item->positionPrevious
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($result));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(false));
        }
    }

    public function delete_car($id)
    {
        header("Access-Control-Allow-Origin: *");
        date_default_timezone_set('America/Caracas');
        if ($id% 2 !== 0){
            $this->api_models->update_by_out($id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(true);
        } else {
            $actual = $this->api_models->get_position($id+1);
            if ($actual->empty){
                $this->api_models->update_by_out($id);
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(true);
            }else {
                $res = $this->api_models->get_all(1);
                if (!empty($res)){
                    $this->delete_out($this->create_position_ramdom(), $actual);
                    $this->api_models->update_by_out($id);
                }
                else {
                    $this->api_models->update_by_out($id);
                    $this->delete_out($id,$actual);
                }
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(true);
            }
        }
    }

    public function delete_out($id, $actual)
    {
        $newPosition = $this->api_models->get_position($id);
        $NumberOfSeconds = $newPosition->time - $this->api_models->calulate_diff_time($actual->dateIngress);
        $newPosition->empty = false;
        $newPosition->id = $actual->id;
        $newPosition->model = $actual->model;
        $newPosition->position = $newPosition->id_table;
        $newPosition->dateIngress = $actual->dateIngress;
        $newPosition->time = $NumberOfSeconds > 0 ? $NumberOfSeconds : 0;
        $newPosition->positionPrevious  = $actual->id_table;

        $actual->empty = true;
        $actual->id = '';
        $actual->model = '';
        $actual->position = -1;
        $actual->dateIngress = null;
        $actual->time = $actual->time + $NumberOfSeconds;
        $actual->numberAccess = $actual->numberAccess + 1;
        $actual->positionPrevious = -1;

        $this->api_models->update_position($newPosition);
        $this->api_models->update_position($actual);
    }
}
