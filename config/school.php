<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Information
    |--------------------------------------------------------------------------
    |
    | Configuration for school information used in contracts and other documents
    |
    */

    'name' => env('SCHOOL_NAME', 'ÉTABLISSEMENT SCOLAIRE'),
    'address' => env('SCHOOL_ADDRESS', 'ADRESSE DE L\'ÉTABLISSEMENT'),
    'phone' => env('SCHOOL_PHONE', 'TÉLÉPHONE DE L\'ÉTABLISSEMENT'),
    'email' => env('SCHOOL_EMAIL', 'contact@school.com'),
    'city' => env('SCHOOL_CITY', 'DAKAR'),
    
    'legal_status' => env('SCHOOL_LEGAL_STATUS', 'STATUT JURIDIQUE DE L\'ÉTABLISSEMENT'),
    'ninea' => env('SCHOOL_NINEA', 'NINEA DE L\'ÉTABLISSEMENT'),
    
    'legal_representative_name' => env('SCHOOL_LEGAL_REP_NAME', 'NOM ET PRÉNOM DU REPRÉSENTANT LÉGAL'),
    'legal_representative_title' => env('SCHOOL_LEGAL_REP_TITLE', 'DIRECTEUR'),
    
    'payment_method' => env('SCHOOL_PAYMENT_METHOD', 'VIREMENT BANCAIRE'),
    'work_hours' => env('SCHOOL_WORK_HOURS', '40'),
    'week_start' => env('SCHOOL_WEEK_START', 'LUNDI'),
    'week_end' => env('SCHOOL_WEEK_END', 'VENDREDI'),
    'tribunal_city' => env('SCHOOL_TRIBUNAL_CITY', 'DAKAR'),
];