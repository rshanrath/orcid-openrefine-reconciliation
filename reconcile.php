<?php

$service_metadata = array(
  "name" => "Simple ORCID Reconciliation",
  "identifierSpace" => "profile.id",
  "schemaSpace" => "profile.id",
  "defaultTypes" => array(
    array(
      "id" => "/profile/id",
      "name" => "Identifier"
    )
  )
);

function search($query){

  $orcid_base = "http://pub.orcid.org/v1.1/search/orcid-bio";

  // Add some additional terms to search on, e.g., an institution
  $query_sweetner = "+AND+University+of+Kansas";

  $ch = curl_init();
  $refine_results = array();
  foreach($query as $qid => $q){
    $refine_results[$qid]["result"] = array();
    
    curl_setopt($ch, CURLOPT_URL, $orcid_base."?q=".urlencode($q->query.$query_sweetner));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $response = curl_exec($ch);

    if (!curl_errno($ch)){
      $xml = simplexml_load_string($response);
      $results = $xml->{'orcid-search-results'}->{'orcid-search-result'};
      foreach ($results as $r){
        $match = 'false';
        // random relevancy threshold for a match
        if ((string) $r->{'relevancy-score'} < 0.65){
          $match = 'true';
        }
        $ret = array(
          'score' => (string) $r->{'relevancy-score'},
          'name' => $r->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'given-names'}.' '.
                $r->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'family-name'},
          'id' => (string) $r->{'orcid-profile'}->{'orcid-identifier'}->{'path'},
          'match' => 'true',
          'type' => array(
            array(
              "id" => "/profile/id",
              "name" => "Identifier"
            )
          )
        );
        $refine_results[$qid]["result"][] = $ret;
      }
    }
  }
  curl_close($ch);
  return $refine_results; 
}

$query = $_REQUEST['queries'];
if ($query){
  $query = json_decode($query);
  $results = search($query);
  if (isset($_REQUEST['callback'])) echo $_REQUEST['callback']."(";
  echo json_encode($results);
  if (isset($_REQUEST['callback'])) echo ")";
  return;
}

if (isset($_REQUEST['callback'])) echo $_REQUEST['callback']."(";
echo json_encode($service_metadata);
if (isset($_REQUEST['callback'])) echo ")";

