# Postman JSON Documentation

**Created on:** 2025-07-25 at 13:49  
 **Purpose:** This document provides a reference Postman JSON object to assist support and development teams during API integration and testing.

---

## 🔍 Why this documentation?

This documentation was created as a temporary support tool **until Swagger (OpenAPI)** is fully integrated into the project. It serves as a bridge for:

- QA and support teams to understand and test API endpoints.
- Developers to debug and verify request/response formats.
- Ensuring consistent communication while Swagger is being set up.

---

##  What's inside?

- A ready-to-import Postman collection in JSON format.
- Example request bodies, headers, and expected responses.
- Notes for common edge cases or usage patterns.

**Teacher Module**

***POST*** 
``
{
    "hire_date":"2022-07-22",
    "user":{
        "first_name": "Mamadou",
        "last_name": "Ndiaye",
        "birthday": "1992-07-14",
        "email": "mamadou.ndiaye@example.sn",
        "password": "Secur@1234",
        "adress": "Sacré-Cœur 3, Dakar",
        "phone": "+221771234567"
    }
}
``
**Response**(if success)
`
{
    "id": 6,
    "hire_date": "2022-07-22",
    "created_at": "2025-07-25T13:45:44.000000Z",
    "updated_at": "2025-07-25T13:45:44.000000Z",
    "user_model_id": 6
}
`
**Parent Module** 
***POST*** 
``
    {
    "user":{
    "first_name": "parent",
    "last_name": "parent",
    "birthday": "1992-07-14",
    "email": "mamadou.ndiaye@example.sn",
    "password": "Secur@1234",
    "adress": "Sacré-Cœur 3, Dakar",
    "phone": "+221771234567"
    }
 }
``
**Response**(if success)
**Session Module**
***POST***
`
    {

    "start_date": "2028",
    "end_date": "2029"
    }
`
**if success**
``
 {
        "id": 3,
        "label": "2028-2029",
        "start_date": "2028",
        "end_date": "2029",
        "status": "en_cours",
        "created_at": "2025-07-25T17:52:53.000000Z",
        "updated_at": "2025-07-25T17:52:53.000000Z"
    }
]
``
