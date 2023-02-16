const csv2json = require('csvjson-csv2json');

const csv = `
transportation_type,description,carbondioxide_kg_mile,carbondioxide_kg_km,methane_g_mile,nitriousoxide_g_mile
air_domestive,Air - Domestic,0.0000000,0.1714700,0.0000000,0.0000000
air_short_haul,Air - Short Haul - Seating Unknown,0.0000000,0.0970000,0.0000000,0.0000000
air_long_haul,Air - Long Haul - Seating Unknown,0.0000000,0.1131900,0.0000000,0.0000000
train,Train - Average (Light Rail and Tram),0.1630000,0.0000000,0.0040000,0.0020000
taxi,Taxi,0.2300000,0.0000000,0.0200000,0.0210000
bus,Bus - Type Unknown,0.1070000,0.0000000,0.0006000,0.0005000
ferry,Large RoPax Ferry,0.0000000,0.1151600,0.0000000,0.0000000
car_gasoline,Passenger Car - Gasoline - Year 2005-present,8.8100000,0.0000000,0.0147000,0.0079000
car_diesel,Passenger Car - Diesel - Year 1983-present,10.1500000,0.0000000,0.0005000,0.0010000
pickupTruckVan_cng,Light Goods Vehicle - CNG,5.9940000,0.0000000,0.7370000,0.0500000
pickupTruckVan_ethanol,Light Goods Vehicle - Ethanol,1.3215000,0.0000000,0.0550000,0.0670000
pickupTruckVan_gasoline,Light Goods Vehicle - Gasoline - Year 2005-present,8.8100000,0.0000000,0.0157000,0.0101000
pickupTruckVan_diesel,Light Goods Vehicle - Diesel - Year 1996-present,10.1500000,0.0000000,0.0010000,0.0015000
deliveryTruck_gasoline,Heavy Duty Vehicle - Rigid - Gasoline - Year 2005-present,8.8100000,0.0000000,0.0326000,0.0177000
deliveryTruck_diesel,Heavy Duty Vehicle - Rigid - Diesel - Year 1960-present,10.1500000,0.0000000,0.0051000,0.0048000
deliveryTruck_cng,Heavy Duty Vehicle - Rigid - CNG,5.9940000,0.0000000,1.9660000,0.1750000
deliveryTruck_ethanol,Heavy Duty Vehicle - Rigid - Ethanol,1.3215000,0.0000000,0.1970000,0.1750000
semiBigRig_gasoline,Heavy Duty Vehicle - Articulated - Gasoline - Year 2005-present,8.8100000,0.0000000,0.0326000,0.0177000
semiBigRig_diesel,Heavy Duty Vehicle - Articulated - Diesel - Year 1960-present,10.1500000,0.0000000,0.0051000,0.0048000
semiBigRig_cng,Heavy Duty Vehicle - Articulated - CNG,5.9940000,0.0000000,1.9660000,0.1750000
semiBigRig_ethanol,Heavy Duty Vehicle - Articulated - Ethanol,1.3215000,0.0000000,0.1970000,0.1750000
`;

const json = csv2json(csv, { parseNumbers: true });
console.log(json);
