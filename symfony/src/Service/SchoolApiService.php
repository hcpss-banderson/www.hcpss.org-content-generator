<?php

namespace App\Service;

/**
 * A class for querying the HCPSS school api.
 */
class SchoolApiService {
    
    /**
     * An array of all school data json decoded.
     *
     * @var array
     */
    private $schools;
    
    /**
     * This is where the api is.
     *
     * @var string
     */
    private $apiEndpoint = 'https://api.hocoschools.org';
    
    public function __construct() {
        $json = file_get_contents("{$this->apiEndpoint}/schools.json");
        $schools = json_decode($json, true)['schools'];
        
        foreach ($schools as $level => $acronyms) {
            foreach ($acronyms as $acronym) {
                $json = file_get_contents("{$this->apiEndpoint}/schools/{$acronym}.json");
                $this->schools[$acronym] = json_decode($json, true);
            }
        }
    }
    
    /**
     * Get awards.
     * 
     * @return array
     */
    public function getAwards(): array {
        $json = file_get_contents("{$this->apiEndpoint}/awards.json");
        return json_decode($json, true);
    }
    
    /**
     * Get the schools data.
     * 
     * @return array
     */
    public function getSchools(): array {
        return $this->schools;
    }
    
    /**
     * Get a list of schools where the key is the school acronym and the value
     * is the property specified by the $path param.
     *
     * For example, if you want a list of school phone numbers:
     * $query = new SchoolQuery();
     * $phoneNumbers = $query->get(['contact', 'phone']);
     *
     * $phoneNumbers contains an array like this:
     * Array(
     *   [arl] => 410-313-6998
     *   [cls] => 410-888-8800
     *   [hc] => 410-313-7081
     *   [aes] => 410-313-6853
     *   etc...
     *
     * @param array $path
     *   Each value in this array should be a key along the path to the property
     *   you are looking for.
     * @return array
     *   a list of schools where the key is the school acronym and the value
     *   is the property specified by the $path param.
     */
    public function get(array $path) {
        $properties = [];
        foreach ($this->schools as $acronym => $school) {
            $property = $school;
            foreach ($path as $key) {
                $property = $property[$key];
            }
            
            $properties[$acronym] = $property;
        }
        
        return $properties;
    }
}