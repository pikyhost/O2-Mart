# Search API Documentation

## Overview

General-purpose search endpoint for all dropdown/select options on the website.

---

## Endpoint

**URL:** `GET /api/search`

**Authentication:** Not required (public endpoint)

---

## Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `type` | string | Yes | Entity type to search (see supported types below) |
| `search` | string | No | Search query (partial match) |
| `limit` | integer | No | Max results (default: 50, max: 100) |
| `filters` | object | No | Additional filters (depends on type) |

---

## Supported Types

| Type | Description | Additional Filters |
|------|-------------|-------------------|
| `brands` | Product brands | - |
| `categories` | Product categories | - |
| `cities` | Cities | - |
| `areas` | Areas/Districts | `city_id` |
| `car_makes` | Car manufacturers | - |
| `car_models` | Car models | `make_id` |
| `countries` | Countries | - |
| `tyres` | Tyre products | - |
| `batteries` | Battery products | - |
| `rims` | Rim products | - |
| `auto_parts` | Auto parts | - |
| `mobile_vans` | Mobile van services | - |
| `installation_centers` | Installation centers | - |
| `tyre_sizes` | Tyre sizes | - |
| `battery_brands` | Battery brands | - |
| `rim_brands` | Rim brands | - |
| `auto_part_brands` | Auto part brands | - |

---

## Response Format

```json
{
  "status": "success",
  "type": "cities",
  "data": [
    {
      "id": 1,
      "label": "Dubai",
      "value": 1
    },
    {
      "id": 2,
      "label": "Abu Dhabi",
      "value": 2
    }
  ]
}
```

---

## Examples

### 1. Search Cities

**Request:**
```http
GET /api/search?type=cities&search=dub
```

**Response:**
```json
{
  "status": "success",
  "type": "cities",
  "data": [
    {
      "id": 1,
      "label": "Dubai",
      "value": 1
    }
  ]
}
```

---

### 2. Search Areas by City

**Request:**
```http
GET /api/search?type=areas&search=al&filters[city_id]=1
```

**Response:**
```json
{
  "status": "success",
  "type": "areas",
  "data": [
    {
      "id": 10,
      "label": "Al Barsha - Dubai",
      "value": 10,
      "city_id": 1,
      "shipping_cost": 0
    },
    {
      "id": 15,
      "label": "Al Quoz - Dubai",
      "value": 15,
      "city_id": 1,
      "shipping_cost": 25
    }
  ]
}
```

---

### 3. Search Car Makes

**Request:**
```http
GET /api/search?type=car_makes&search=toy
```

**Response:**
```json
{
  "status": "success",
  "type": "car_makes",
  "data": [
    {
      "id": 5,
      "label": "Toyota",
      "value": 5
    }
  ]
}
```

---

### 4. Search Car Models by Make

**Request:**
```http
GET /api/search?type=car_models&search=cam&filters[make_id]=5
```

**Response:**
```json
{
  "status": "success",
  "type": "car_models",
  "data": [
    {
      "id": 123,
      "label": "Camry (Toyota)",
      "value": 123,
      "make_id": 5
    }
  ]
}
```

---

### 5. Search Brands

**Request:**
```http
GET /api/search?type=brands&search=mich&limit=10
```

**Response:**
```json
{
  "status": "success",
  "type": "brands",
  "data": [
    {
      "id": 8,
      "label": "Michelin",
      "value": 8
    }
  ]
}
```

---

### 6. Search Tyres

**Request:**
```http
GET /api/search?type=tyres&search=195/65
```

**Response:**
```json
{
  "status": "success",
  "type": "tyres",
  "data": [
    {
      "id": 456,
      "label": "Michelin Energy XM2 195/65 R15",
      "value": 456,
      "price": 285.50
    }
  ]
}
```

---

### 7. Search Mobile Vans

**Request:**
```http
GET /api/search?type=mobile_vans&search=van
```

**Response:**
```json
{
  "status": "success",
  "type": "mobile_vans",
  "data": [
    {
      "id": 1,
      "label": "Van A - Sharjah – Al Wahda Street",
      "value": 1
    },
    {
      "id": 2,
      "label": "Van B - Dubai – Al Quoz",
      "value": 2
    }
  ]
}
```

---

### 8. Get All Categories (No Search)

**Request:**
```http
GET /api/search?type=categories
```

**Response:**
```json
{
  "status": "success",
  "type": "categories",
  "data": [
    {
      "id": 1,
      "label": "Tyres",
      "value": 1,
      "parent_id": null
    },
    {
      "id": 2,
      "label": "Summer Tyres",
      "value": 2,
      "parent_id": 1
    }
  ]
}
```

---

## Error Responses

### Validation Error

**Status Code:** `422 Unprocessable Entity`

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "type": [
      "The type field is required."
    ]
  }
}
```

### Invalid Type

**Status Code:** `422 Unprocessable Entity`

```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "type": [
      "The selected type is invalid."
    ]
  }
}
```

### Server Error

**Status Code:** `500 Internal Server Error`

```json
{
  "status": "error",
  "message": "Search failed: Database connection error"
}
```

---

## Frontend Integration Examples

### JavaScript (Fetch API)

```javascript
// Search cities
async function searchCities(query) {
  const response = await fetch(`/api/search?type=cities&search=${encodeURIComponent(query)}`);
  const data = await response.json();
  return data.data; // Array of { id, label, value }
}

// Search areas by city
async function searchAreas(query, cityId) {
  const url = `/api/search?type=areas&search=${encodeURIComponent(query)}&filters[city_id]=${cityId}`;
  const response = await fetch(url);
  const data = await response.json();
  return data.data;
}
```

### React Example (with useState/useEffect)

```jsx
import { useState, useEffect } from 'react';

function SearchableSelect({ type, filters = {}, onChange }) {
  const [options, setOptions] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const searchOptions = async () => {
      setLoading(true);
      try {
        const params = new URLSearchParams({
          type,
          search,
          ...Object.entries(filters).reduce((acc, [key, val]) => {
            acc[`filters[${key}]`] = val;
            return acc;
          }, {})
        });
        
        const response = await fetch(`/api/search?${params}`);
        const data = await response.json();
        
        if (data.status === 'success') {
          setOptions(data.data);
        }
      } catch (error) {
        console.error('Search failed:', error);
      } finally {
        setLoading(false);
      }
    };

    const debounce = setTimeout(searchOptions, 300);
    return () => clearTimeout(debounce);
  }, [type, search, filters]);

  return (
    <select onChange={(e) => onChange(e.target.value)}>
      <option value="">Select...</option>
      {options.map(opt => (
        <option key={opt.id} value={opt.value}>
          {opt.label}
        </option>
      ))}
    </select>
  );
}

// Usage
<SearchableSelect 
  type="areas" 
  filters={{ city_id: selectedCityId }}
  onChange={handleAreaChange}
/>
```

---

## Benefits

✅ **Single endpoint** for all dropdown searches  
✅ **Consistent response format** across all types  
✅ **Built-in filtering** for dependent dropdowns (city → areas, make → models)  
✅ **Performance optimized** with limits and indexing  
✅ **Type-safe** with validation  
✅ **Easy to extend** - just add new types to the controller

---

## Notes

1. **Search is case-insensitive** and uses partial matching (`LIKE %query%`)
2. **Only active records** are returned for areas, mobile vans, and installation centers
3. **Results are limited** to prevent performance issues (default 50, max 100)
4. **Additional filters** are type-specific (see table above)
5. **No authentication required** - public endpoint for better UX
