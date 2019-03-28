<?php
/**
 * Created by IntelliJ IDEA.
 * User: memeo
 * Date: 31/01/19
 * Time: 02:46 AM
 */

class Api_models extends CI_Model {


    function update_position_register($id)
    {
        $actuallYposition = $this->get_position($id);
        date_default_timezone_set('America/Caracas');
        $data = array(
            'empty' => false,
            'id' =>  $this->input->post('id'),
            'model' => $this->input->post('model'),
            'position' => $id,
            'dateIngress' => date('Y-m-d H:i:s', time()),
            'departureDate' => null,
            'time' => $actuallYposition->time,
            'numberAccess' => $actuallYposition->numberAccess,
            'positionPrevious' => $id
        );
        $this->db->where('id_table', $id);
        $this->db->update('car',$data);
    }

    function get_all($type)
    {
        switch ($type){
            case 0:
                $query = $this->db->query('SELECT * FROM car ORDER BY id_table ');
                break;
            case 1:
                $query = $this->db->query('SELECT id_table FROM car  where empty = 1 ORDER BY id_table ');
                break;
            default:
                null;
        }
        return $query->result();
    }

    function get_position($id)
    {
        $query = $this->db->query('SELECT * FROM car  WHERE `id_table`='.$id);
        return $query->row();
    }

    function insert_position($data)
    {
        $this->db->insert('car',$data);
        return $this->db->insert_id();
    }

    function get_first_five()
    {
        $query = $this->db->query('SELECT * FROM car as a ORDER BY a.time DESC LIMIT 5');
        return $query->result();
    }

    function get_position_by_licence_plate($id)
    {
        $this->db->select('*');
        $this->db->where('id',$id);
        $q=$this->db->get('car');
        $count=$q->result();
        return $count;
    }

    public function update_by_out($id)
    {
        $NumberOfSeconds = $this->calulate_diff_time($this->input->post('dateIngress')) + $this->input->post('time');
        $data = array(
            'empty' => true,
            'id' => '',
            'model' => '',
            'position' => -1,
            'dateIngress' => null,
            'departureDate' =>null,
            'time' => $NumberOfSeconds,
            'numberAccess' => $this->input->post('numberAccess') + 1,
            'positionPrevious' => -1
        );
        $this->db->where('id_table', $id);
        $this->db->update('car',$data);
    }

    public function calulate_diff_time($item)
    {
        return strtotime(date('Y-m-d H:i:s', time())) - strtotime($item);
    }

    public function update_position($data)
    {
        $this->db->where('id_table', $data->id_table);
        $this->db->update('car',$data);
    }
}
