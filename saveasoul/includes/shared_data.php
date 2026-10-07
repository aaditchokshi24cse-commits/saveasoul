<?php
// =====================================================
// Shared data: Indian cities list and common hospital
// names, used for datalist suggestions across the site.
// Also holds small reusable validation helper functions.
// =====================================================

$indian_cities = [
    "Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Gandhinagar", "Anand",
    "Nadiad", "Mehsana", "Morbi", "Bharuch", "Navsari", "Valsad", "Vapi", "Junagadh", "Porbandar",
    "Gandhidham", "Veraval", "Godhra", "Palanpur", "Patan", "Himatnagar", "Botad",
    "Mumbai", "Pune", "Nagpur", "Nashik", "Thane", "Aurangabad", "Solapur", "Kolhapur", "Amravati",
    "Delhi", "New Delhi", "Noida", "Gurugram", "Faridabad", "Ghaziabad",
    "Bengaluru", "Mysuru", "Hubballi", "Mangaluru", "Belagavi",
    "Chennai", "Coimbatore", "Madurai", "Tiruchirappalli", "Salem",
    "Hyderabad", "Warangal", "Visakhapatnam", "Vijayawada", "Guntur",
    "Kolkata", "Howrah", "Durgapur", "Siliguri", "Asansol",
    "Jaipur", "Jodhpur", "Udaipur", "Kota", "Ajmer", "Bikaner",
    "Lucknow", "Kanpur", "Varanasi", "Agra", "Meerut", "Allahabad", "Noida", "Ghaziabad", "Bareilly",
    "Bhopal", "Indore", "Jabalpur", "Gwalior", "Ujjain",
    "Patna", "Gaya", "Muzaffarpur", "Bhagalpur",
    "Chandigarh", "Ludhiana", "Amritsar", "Jalandhar", "Patiala",
    "Bhubaneswar", "Cuttack", "Rourkela",
    "Guwahati", "Dibrugarh", "Silchar",
    "Raipur", "Bhilai", "Bilaspur",
    "Ranchi", "Jamshedpur", "Dhanbad",
    "Thiruvananthapuram", "Kochi", "Kozhikode", "Thrissur", "Kollam",
    "Dehradun", "Haridwar", "Rishikesh",
    "Shimla", "Manali",
    "Srinagar", "Jammu",
    "Panaji", "Margao",
    "Imphal", "Shillong", "Agartala", "Aizawl", "Kohima", "Itanagar", "Gangtok",
    "Puducherry", "Port Blair"
];

$common_hospitals = [
    "Civil Hospital Ahmedabad", "Sterling Hospital Ahmedabad", "Apollo Hospital Ahmedabad",
    "Zydus Hospital Ahmedabad", "CIMS Hospital Ahmedabad", "Shalby Hospital Ahmedabad",
    "V S Hospital Ahmedabad", "SAL Hospital Ahmedabad", "HCG Cancer Centre Ahmedabad",
    "Red Cross Blood Bank Ahmedabad",
    "AIIMS Delhi", "Safdarjung Hospital Delhi", "Fortis Hospital Delhi", "Max Super Speciality Hospital Delhi",
    "Apollo Hospital Delhi", "Sir Ganga Ram Hospital Delhi",
    "KEM Hospital Mumbai", "Lilavati Hospital Mumbai", "Kokilaben Dhirubhai Ambani Hospital Mumbai",
    "Tata Memorial Hospital Mumbai", "Fortis Hospital Mumbai",
    "Manipal Hospital Bengaluru", "Narayana Health Bengaluru", "Apollo Hospital Bengaluru",
    "Fortis Hospital Bengaluru",
    "Apollo Hospital Chennai", "Fortis Malar Hospital Chennai", "MIOT International Chennai",
    "Government General Hospital Chennai",
    "Apollo Hospital Hyderabad", "Yashoda Hospital Hyderabad", "NIMS Hyderabad",
    "CMC Vellore",
    "PGIMER Chandigarh",
    "SMS Hospital Jaipur", "Fortis Escorts Hospital Jaipur",
    "SGPGI Lucknow", "KGMU Lucknow",
    "Other / Not Listed"
];

function is_valid_phone($phone)
{
    return preg_match('/^[0-9]{10}$/', $phone);
}

function is_valid_email_format($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
