# Frontend Dropdown Integration Guide

## Overview

All search forms on your website should use the `/api/search` endpoint for searchable dropdowns.

---

## 🎯 Forms to Update

### 1. **Rims Page** (`/Rims`)

**Dropdowns:**
- Car Make → `type=car_makes`
- Model → `type=car_models&filters[make_id]={selected_make}`
- Year → `type=car_years`

```javascript
// Car Make dropdown
fetch('/api/search?type=car_makes&search=' + userInput)

// Model dropdown (dependent on make)
fetch('/api/search?type=car_models&search=' + userInput + '&filters[make_id]=' + selectedMakeId)

// Year dropdown
fetch('/api/search?type=car_years&search=' + userInput)
```

---

### 2. **Tyres Page** (`/Tyres`)

#### **Search by Size Tab**

**Dropdowns:**
- Width → `type=tyre_widths`
- Height → `type=tyre_heights`
- RIM Size → `type=tyre_rim_sizes`

```javascript
// Width dropdown
fetch('/api/search?type=tyre_widths&search=' + userInput)

// Height dropdown
fetch('/api/search?type=tyre_heights&search=' + userInput)

// RIM Size dropdown
fetch('/api/search?type=tyre_rim_sizes&search=' + userInput)
```

#### **Search by Car Tab**

**Dropdowns:**
- Car Make → `type=car_makes`
- Model → `type=car_models&filters[make_id]={selected_make}`
- Year → `type=car_years`
- Trim → `type=car_trims&filters[model_id]={selected_model}&filters[year]={selected_year}`

```javascript
// Car Make dropdown
fetch('/api/search?type=car_makes&search=' + userInput)

// Model dropdown (dependent on make)
fetch('/api/search?type=car_models&search=' + userInput + '&filters[make_id]=' + selectedMakeId)

// Year dropdown
fetch('/api/search?type=car_years&search=' + userInput)

// Trim dropdown (dependent on model AND year)
fetch('/api/search?type=car_trims&search=' + userInput + '&filters[model_id]=' + selectedModelId + '&filters[year]=' + selectedYear)
```

---

### 3. **Batteries Page** (`/Batteries`)

**Dropdowns:**
- Car Make → `type=car_makes`
- Model → `type=car_models&filters[make_id]={selected_make}`
- Year → `type=car_years`

```javascript
// Same as Rims page
fetch('/api/search?type=car_makes&search=' + userInput)
fetch('/api/search?type=car_models&search=' + userInput + '&filters[make_id]=' + selectedMakeId)
fetch('/api/search?type=car_years&search=' + userInput)
```

---

### 4. **Auto Parts Page** (`/autoparts`)

**Dropdowns:**
- Car Make → `type=car_makes`
- Model → `type=car_models&filters[make_id]={selected_make}`
- Year → `type=car_years`

```javascript
// Same as Rims page
fetch('/api/search?type=car_makes&search=' + userInput)
fetch('/api/search?type=car_models&search=' + userInput + '&filters[make_id]=' + selectedMakeId)
fetch('/api/search?type=car_years&search=' + userInput)
```

---

## 🎨 React Component Example

### **SearchableSelect Component**

```jsx
import { useState, useEffect } from 'react';

function SearchableSelect({ 
  type, 
  filters = {}, 
  placeholder = "Select...", 
  onChange,
  value 
}) {
  const [options, setOptions] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(false);
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    const fetchOptions = async () => {
      setLoading(true);
      try {
        const params = new URLSearchParams({
          type,
          search,
          limit: 50,
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

    const debounce = setTimeout(fetchOptions, 300);
    return () => clearTimeout(debounce);
  }, [type, search, JSON.stringify(filters)]);

  return (
    <div className="relative">
      <input
        type="text"
        placeholder={placeholder}
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        onFocus={() => setIsOpen(true)}
        onBlur={() => setTimeout(() => setIsOpen(false), 200)}
        className="w-full px-4 py-2 border rounded"
      />
      
      {loading && <div className="absolute right-3 top-3">Loading...</div>}
      
      {isOpen && options.length > 0 && (
        <div className="absolute z-10 w-full mt-1 bg-white border rounded shadow-lg max-h-60 overflow-auto">
          {options.map(option => (
            <div
              key={option.id}
              onClick={() => {
                onChange(option);
                setSearch(option.label);
                setIsOpen(false);
              }}
              className="px-4 py-2 hover:bg-gray-100 cursor-pointer"
            >
              {option.label}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default SearchableSelect;
```

---

## 🎯 Usage Example: Tyres Page (Search by Car)

```jsx
import SearchableSelect from './SearchableSelect';
import { useState } from 'react';

function TyresSearchByCar() {
  const [selectedMake, setSelectedMake] = useState(null);
  const [selectedModel, setSelectedModel] = useState(null);
  const [selectedYear, setSelectedYear] = useState(null);
  const [selectedTrim, setSelectedTrim] = useState(null);

  return (
    <div className="grid grid-cols-4 gap-4">
      {/* Car Make */}
      <SearchableSelect
        type="car_makes"
        placeholder="Car Make"
        onChange={(option) => {
          setSelectedMake(option);
          setSelectedModel(null); // Reset dependent fields
          setSelectedTrim(null);
        }}
      />

      {/* Model (dependent on make) */}
      <SearchableSelect
        type="car_models"
        placeholder="Model"
        filters={{ make_id: selectedMake?.id }}
        onChange={(option) => {
          setSelectedModel(option);
          setSelectedTrim(null); // Reset dependent field
        }}
        disabled={!selectedMake}
      />

      {/* Year */}
      <SearchableSelect
        type="car_years"
        placeholder="Year"
        onChange={(option) => {
          setSelectedYear(option);
          setSelectedTrim(null); // Reset dependent field
        }}
      />

      {/* Trim (dependent on model AND year) */}
      <SearchableSelect
        type="car_trims"
        placeholder="Trim"
        filters={{ 
          model_id: selectedModel?.id,
          year: selectedYear?.value
        }}
        onChange={setSelectedTrim}
        disabled={!selectedModel || !selectedYear}
      />
    </div>
  );
}
```

---

## 🎯 Usage Example: Tyres Page (Search by Size)

```jsx
import SearchableSelect from './SearchableSelect';
import { useState } from 'react';

function TyresSearchBySize() {
  const [selectedWidth, setSelectedWidth] = useState(null);
  const [selectedHeight, setSelectedHeight] = useState(null);
  const [selectedRimSize, setSelectedRimSize] = useState(null);

  return (
    <div className="grid grid-cols-3 gap-4">
      {/* Width */}
      <SearchableSelect
        type="tyre_widths"
        placeholder="Width"
        onChange={setSelectedWidth}
      />

      {/* Height */}
      <SearchableSelect
        type="tyre_heights"
        placeholder="Height"
        onChange={setSelectedHeight}
      />

      {/* RIM Size */}
      <SearchableSelect
        type="tyre_rim_sizes"
        placeholder="RIM Size"
        onChange={setSelectedRimSize}
      />
    </div>
  );
}
```

---

## 📋 Complete Mapping Table

| Page | Dropdown | API Type | Filters |
|------|----------|----------|---------|
| **Rims** | Car Make | `car_makes` | - |
| **Rims** | Model | `car_models` | `make_id` |
| **Rims** | Year | `car_years` | - |
| **Tyres (By Size)** | Width | `tyre_widths` | - |
| **Tyres (By Size)** | Height | `tyre_heights` | - |
| **Tyres (By Size)** | RIM Size | `tyre_rim_sizes` | - |
| **Tyres (By Car)** | Car Make | `car_makes` | - |
| **Tyres (By Car)** | Model | `car_models` | `make_id` |
| **Tyres (By Car)** | Year | `car_years` | - |
| **Tyres (By Car)** | Trim | `car_trims` | `model_id`, `year` |
| **Batteries** | Car Make | `car_makes` | - |
| **Batteries** | Model | `car_models` | `make_id` |
| **Batteries** | Year | `car_years` | - |
| **Auto Parts** | Car Make | `car_makes` | - |
| **Auto Parts** | Model | `car_models` | `make_id` |
| **Auto Parts** | Year | `car_years` | - |

---

## ✅ Benefits

✅ **Single API endpoint** for all dropdowns  
✅ **Searchable** - Users can type to filter options  
✅ **Consistent UX** across all pages  
✅ **Dependent dropdowns** work automatically with filters  
✅ **Performance optimized** with debouncing and limits  
✅ **Easy to maintain** - Changes to backend apply everywhere

---

## 🚀 Next Steps

1. **Replace existing dropdown implementations** with SearchableSelect component
2. **Test each form** to ensure dropdowns work correctly
3. **Add loading states** for better UX
4. **Handle empty states** when no results found
5. **Add error handling** for API failures
