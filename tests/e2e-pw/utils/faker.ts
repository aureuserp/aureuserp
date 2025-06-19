export function generateDescription(length = 255) {
    const phrases = [
        "This data is created solely for internal testing purposes. Not for production use.",
        "Test entry generated for QA validation and system behavior checks.",
        "Temporary data for testing workflows, not a real transaction or activity.",
        "A perfect blend of style and power.",
        "Sample record created to verify system functionality during testing.",
        "Test data. Please ignore used for development and testing scenarios only.",
        "Created for functional testing of module features. Not valid for business operations.",
        "This is a dummy entry used in sandbox/testing environment for process validation.",
        "Entry used in regression testing to verify expected system responses.",
        "Automated test data generated as part of the test suite execution.",
    ];

    let description = "";

    while (description.length < length) {
        let phrase = phrases[Math.floor(Math.random() * phrases.length)];

        if (description.length + phrase.length <= length) {
            description += (description ? " " : "") + phrase;
        } else {
            description +=
                " " + phrase.substring(0, length - description.length);
            break;
        }
    }

    return description.trim();
}

export function generateCategory() {
    const category = [
        "Fashion & Apparel",
        "Electronics & Gadgets",
        "Health & Beauty",
        "Home & Furniture",
        "Grocery & Food",
        "Sports & Fitness",
        "Toys & Games",
        "Automotive & Accessories",
        "Books & Stationery",
        "Jewelry & Watches",
    ];

    return category[Math.floor(Math.random() * category.length)];
}

export function generateProductName() {
    const products = [
        "Wireless Earbuds",
        "Smartphone Case",
        "Running Shoes",
        "Gaming Laptop",
        "Organic Green Tea",
        "Fitness Tracker",
        "Leather Wallet",
        "Bluetooth Speaker",
        "Scented Candles",
        "Electric Kettle",
    ];

    return products[Math.floor(Math.random() * products.length)];
}

export function generateName() {
    const names = [
        "John Doe",
        "Jane Smith",
        "Alice Johnson",
        "Bob Brown",
        "Charlie Davis",
        "Diana Evans",
        "Ethan Wilson",
        "Fiona Martinez",
        "George Garcia",
        "Hannah Lee",
        "Isabella Rodriguez",
        "Jack White",
        "Kevin Taylor",
        "Laura Anderson",
        "Michael Thomas",
        "Nina Clark",
        "Oliver Lewis",
        "Paula Walker",
        "Quentin Hall",
        "Rachel Young",
        "Samuel King",
        "Tina Wright",
        "Ursula Scott",
        "Victor Green",
    ];

    return names[Math.floor(Math.random() * names.length)];
}

export function generateAccountNumber() {
    const length = 10;
    let accountNumber = '';
    for (let i = 0; i < length; i++) {
        accountNumber += Math.floor(Math.random() * 10);
    }
    return accountNumber;
}

export function generateEmail() {
    const domains = ["example.com", "test.com", "demo.com"];
    const randomName = Math.random().toString(36).substring(2, 7);
    return `${randomName}@${domains[Math.floor(Math.random() * domains.length)]}`;
}

export function generatePhoneNumber() {
    const areaCode = Math.floor(Math.random() * 900) + 100; // 100-999
    const centralOfficeCode = Math.floor(Math.random() * 900) + 100; // 100-999
    const lineNumber = Math.floor(Math.random() * 9000) + 1000; // 1000-9999
    return `(${areaCode}) ${centralOfficeCode}-${lineNumber}`;
}

export function generateAddress() {
    const streets = [
        "Main St",
        "Broadway",
        "1st Ave",
        "2nd St",
        "3rd Blvd",
        "4th Rd",
        "5th Ln",
        "6th Dr",
        "7th Ct",
        "8th Pl",
    ];
    const cities = [
        "New York",
        "Los Angeles",
        "Chicago",
        "Houston",
        "Phoenix",
        "Philadelphia",
        "San Antonio",
        "San Diego",
        "Dallas",
        "San Jose",
    ];
    const states = [
        "NY",
        "CA",
        "IL",
        "TX",
        "AZ",
        "PA",
        "FL",
        "OH",
        "GA",
        "NC",
    ];
    const countries = [

        "Canada",
        "UK",
        "Australia",
        "Germany",
        "France",
        "India",
        "Japan",
        "Brazil",
        "South Africa",
    ];
    const zipCodes = [
        "10001",
        "90001",
        "60601",
        "77001",
        "85001",
        "19101",
        "33101",
        "44101",
        "30301",
        "28201",
    ];

    const randomStreet = streets[Math.floor(Math.random() * streets.length)];
    const randomNumber = Math.floor(Math.random() * 1000) + 1;
    const randomCity = cities[Math.floor(Math.random() * cities.length)];
    const randomState = states[Math.floor(Math.random() * states.length)];
    const randomCountry = countries[Math.floor(Math.random() * countries.length)];
    const randomZip = zipCodes[Math.floor(Math.random() * zipCodes.length)];

    return {
        street: `${randomNumber} ${randomStreet}`,
        city: randomCity,
        state: randomState,
        country: randomCountry,
        zip: randomZip
    };
}

export function generateBankName() {
    const banks = [
        "Bank of America",
        "Chase Bank",
        "Wells Fargo",
        "Citibank",
        "PNC Bank",
        "Capital One",
        "TD Bank",
        "US Bank",
        "BB&T Bank",
        "SunTrust Bank",
    ];

    return banks[Math.floor(Math.random() * banks.length)];
}

export function generateIncoterms() {
    const incoterms = [
        "EXW (Ex Works)",
        "FCA (Free Carrier)",
        "CPT (Carriage Paid To)",
        "CIP (Carriage and Insurance Paid To)",
        "DAT (Delivered at Terminal)",
        "DAP (Delivered at Place)",
        "DDP (Delivered Duty Paid)",
        "FOB (Free on Board)",
        "CFR (Cost and Freight)",
        "CIF (Cost, Insurance, and Freight)",
    ];

    return incoterms[Math.floor(Math.random() * incoterms.length)];
}

export function generateTerm() {
    const terms = [
        "Net 30",
        "Net 60",
        "Net 90",
        "Due on Receipt",
        "End of Month (EOM)",
        "Cash in Advance (CIA)",
        "Cash on Delivery (COD)",
        "Letter of Credit (LC)",
        "Documentary Collection (D/C)",
        "Open Account",
    ];

    return terms[Math.floor(Math.random() * terms.length)];
}

export function generateTaxGroup() {
    const taxGroups = [
        "Standard Rate",
        "Reduced Rate",
        "Zero Rate",
        "Exempt",
        "Outside Scope",
        "Input Tax Credit",
        "Reverse Charge",
        "Flat Rate Scheme",
        "Margin Scheme",
        "Tour Operators Margin Scheme (TOMS)",
    ];

    return taxGroups[Math.floor(Math.random() * taxGroups.length)];
}

export function generateTaxName() {
    const taxNames = [
        "VAT",
        "GST",
        "Sales Tax",
        "Service Tax",
        "Excise Duty",
        "Customs Duty",
        "Withholding Tax",
        "Capital Gains Tax",
        "Corporate Tax",
        "Income Tax",
    ];

    return taxNames[Math.floor(Math.random() * taxNames.length)];
}

export function generateAttribute() {
    const attributes = {
        Size: ['S', 'M', 'L'],
        Color: ['Red', 'Black', 'Yellow'],
        Material: ['Cotton', 'Polyester', 'Wool'],
        Fit: ['Regular', 'Slim', 'Loose']
    };

    const attributeKeys = Object.keys(attributes);
    const randomAttribute = attributeKeys[Math.floor(Math.random() * attributeKeys.length)];
    const values = attributes[randomAttribute];

    return {
        attribute: randomAttribute,
        values: values
    };
}

export function generateActivityPlans() {
    const activities = [
        "Q1 Marketing Strategy",
        "Q2 Product Development",
        "Q3 Sales Strategy",
        "Q4 Customer Retention",
        "Annual Budget Planning",
        "Quarterly Review Meeting",
        "Monthly Team Building Activity",
        "Weekly Sales Training",
        "Daily Standup Meeting",
        "Bi-Weekly Project Update"
    ];

    return activities[Math.floor(Math.random() * activities.length)];
}

export function generateActivityTypes() {
    const activityTypes = [
        "Meeting",
        "Workshop",
        "Training",
        "Conference",
        "Webinar",
        "Team Building",
        "Networking Event",
        "Product Launch",
        "Sales Pitch",
        "Customer Feedback Session"
    ];

    return activityTypes[Math.floor(Math.random() * activityTypes.length)];
}

export function generatePackageTypes() {
    const packageTypes = [
        "Box",
        "Envelope",
        "Pallet",
        "Crate",
        "Drum",
        "Bag",
        "Carton",
        "Roll",
        "Tube",
        "Shrink Wrap"
    ];

    return packageTypes[Math.floor(Math.random() * packageTypes.length)];
}

export function generateVendorName() {
    const vendorNames = [
        "ABC Supplies",
        "XYZ Distributors",
        "Global Traders",
        "Local Market",
        "Quality Goods Co.",
        "Fast Shipping Inc.",
        "Reliable Vendors",
        "Trusted Suppliers",
        "Wholesale Warehouse",
        "Direct Importers"
    ];

    return vendorNames[Math.floor(Math.random() * vendorNames.length)];
}