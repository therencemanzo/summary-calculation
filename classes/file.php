<?php

require_once('db.php');
require_once('filedata.php');
require_once('session.php');

class File extends db{

    private $file;
    private $uploadDirectory = 'uploads/';
    private $csvFileName = '';
    private $fileName;
    public $user_id = 0;
    public $file_id;

    public function __construct(){
        parent::__construct('csvfile');

        $session = new Session();
        $user = $session->getSession('user');

        if($user !== null){
            $this->user_id =  $user;
        }
    }
    
    private function setFilename(){
        $this->csvFileName = hash('sha256', $this->file['name'].time()).'-'.date('Y-m-d').'.csv';
    }
    public function generateSummary($file){

        $this->file = $file;
        $this->setFilename();
        //validate the format
        $expensesArray = array_map('str_getcsv', file($this->file['tmp_name']));
        foreach($expensesArray as $key => $expense){
            $lenght = count($expense);
            if($lenght !== 3){
                return false;
            }
        }

        if (move_uploaded_file($this->file['tmp_name'], $this->uploadDirectory.$this->csvFileName)) {

            $data = array(
                'filename' => $this->csvFileName,
                'user_id' => $this->user_id,
                'original_filename' => $this->file['name']
            );

            $this->file_id = $this->insert($data);

            $expensesArray = array_map('str_getcsv', file($this->uploadDirectory.$this->csvFileName));

            $summary = array();

            $fileData = new FileData();

            foreach($expensesArray as $key => $expense){

                $description = ucfirst(strtolower($expense[0]));
                
                $amount = number_format($expense[1], 2, '.', ',');
                $quantity = $expense[2];
    
                $data = array(
                    'description' => $description, 
                    'amount' => $amount, 
                    'quantity' => $quantity,
                    'file_id' => $this->file_id
                );

                $total = $amount * $quantity;

                if(!isset($summary[$description])){
                    $summary[$description] = number_format((float)$total, 2, '.', ',');
                }else{
                    $summary[$description] =  number_format((float)$summary[$description] + (float)$total, 2, '.', ',');
                }
               
                $fileData->insert($data);

            }

            unset($fileData);

            if($this->user_id == 0){

                $session = new Session();
                $generatedFile = $session->getSession('generatedFile');
                $generated = [];
                if($generatedFile !== null){
                    $generated = $generatedFile;
                    $generated [] = $this->file_id;
                    $session->storeSession(array('generatedFile' => $generated));
                }else{
                    $generated [] = $this->file_id;
                    $session->storeSession(array('generatedFile' => $generated));
                }

                unset($session);
            }

            $data = array(
                'id' => $this->file_id,
                'summary' => json_encode($summary)
            );

            $this->update($data);

            return $data;

        }else{

            return false;

        }
    }

    public function getFiles($page = 0){
        $session = new Session();
        $userId = $session->getSession('user');
        unset($session);

        if($page != 0){

            $limit = 10;
            $page = $page - 1;
            $skip = $limit * $page;
            
        }

        $uploads = $this->fetchAll([], array('user_id' => $userId), array('id' => 'DESC'), $limit, $skip);


        return $uploads;
    }

    public function getSummary($file_id){
        return $this->select( ['summary'], array('id' => $file_id) );
    }

    public function exportSummary($file_id){

        ob_start();

        $filename = 'exported-csv-'.date('Y-m-d').'-'.time(). '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='. $filename);

        $header_args = array( 'Description', 'Total' );

        $result = $this->getSummary($file_id);
        $summary = json_decode($result['summary']);
        $datas = [];
        foreach($summary as $description => $amount){
            $datas  []= array( $description,$amount);
        }

        ob_end_clean();

        $output = fopen( 'php://output', 'w' );
        fputcsv( $output, $header_args );

        foreach( $datas as $data ){
            fputcsv( $output, $data );
        }

        fclose( $output );
        exit;

    }


}