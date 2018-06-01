<?php

namespace App\Repositories;

use SafeStudio\Firebase\Firebase;

class FirebaseRepository {

    // create the firebase user 

    public function createFirebaseUser($data) {

        $firebaseObj = new Firebase();
        $firebaseObj->update('/users/user_' . $data['id'], $data);
    }

    
    // set the conversation in firebase by replacing parent data
    public function setConversationData($conversionId, $data) {

        $firebaseObj = new Firebase();
        return $firebaseObj->update('/conversations/' . $conversionId, $data);
    }

    // get the data with conversation id provided
    public function getConversationData($conversionId) {
        $firebaseObj = new Firebase();
        return $firebaseObj->get('/conversations/' . $conversionId);
    }

    // push the data into existing conversation id
    public function pushConversationData($conversionId) {
        $firebaseObj = new Firebase();
        return $firebaseObj->push('/conversations/' . $conversionId);
    }
    
   // push the data into existing conversation id
    public function listConversationListing() {
        $firebaseObj = new Firebase();
        return $firebaseObj->get('/conversations/');
    }
    
    // delete  particular conversation id
    public function deleteConversationData($conversionId) {
        $firebaseObj = new Firebase();
        return $firebaseObj->delete('/conversations/' . $conversionId);
    }
    
    // create firebase group
    public function createFireBaseGroup($data) {
        $firebaseObj = new Firebase();
        return $firebaseObj->push('/group/',$data);
    }
    
    // update firebase group
     public function updateFireBaseGroupConversation($data) {
        $firebaseObj = new Firebase();
        return $firebaseObj->push('/group/',$data);
    }
    
    // delete  particular conversation id
    public function deleteGroupData($conversionId) {
        $firebaseObj = new Firebase();
        return $firebaseObj->delete('/group/' . $conversionId);
    }
    
     public function deleteMessagesdata($conversionId) {
        $firebaseObj = new Firebase();
        return $firebaseObj->delete('/messages/' . $conversionId);
    }
    

      // set the conversation in firebase by replacing parent data
    public function updateFireBaseGroupData($conversionId, $data) {

        $firebaseObj = new Firebase();
        return $firebaseObj->update('/group/' . $conversionId, $data);
    }

    
}
