// Lebanese orders with realistic data
const mockupOrders = [
    {
        id: "ORD-001",
        date: "2023-05-15",
        customer: "Hassan Nasrallah",
        phone: "+961 3 123 456",
        pickupLocation: "Hamra St, Beirut",
        deliveryLocation: "Bliss St, Beirut",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 45.99,
    },
    {
        id: "ORD-002",
        date: "2023-05-14",
        customer: "Zeinab Khalil",
        phone: "+961 70 987 654",
        pickupLocation: "Verdun, Beirut",
        deliveryLocation: "Ashrafieh, Beirut",
        status: "processing",
        paymentStatus: "paid",
        amount: 78.5,
    },
    {
        id: "ORD-003",
        date: "2023-05-13",
        customer: "Ali Haidar",
        phone: "+961 71 456 789",
        pickupLocation: "Jounieh, Mount Lebanon",
        deliveryLocation: "Byblos, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 125.75,
    },
    {
        id: "ORD-004",
        date: "2023-05-12",
        customer: "Sara Khoury",
        phone: "+961 76 789 012",
        pickupLocation: "Tripoli, North Lebanon",
        deliveryLocation: "Batroun, North Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 67.25,
    },
    {
        id: "ORD-005",
        date: "2023-05-11",
        customer: "Mohammad Ayyoub",
        phone: "+961 3 234 567",
        pickupLocation: "Sidon, South Lebanon",
        deliveryLocation: "Tyre, South Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 92.0,
    },
    {
        id: "ORD-006",
        date: "2023-05-10",
        customer: "Layla Abboud",
        phone: "+961 78 345 678",
        pickupLocation: "Zahle, Bekaa Valley",
        deliveryLocation: "Baalbek, Bekaa Valley",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 54.25,
    },
    {
        id: "ORD-007",
        date: "2023-05-09",
        customer: "Karim Mansour",
        phone: "+961 79 567 890",
        pickupLocation: "Nabatieh, South Lebanon",
        deliveryLocation: "Marjayoun, South Lebanon",
        status: "processing",
        paymentStatus: "partial",
        amount: 135.50,
    },
    {
        id: "ORD-008",
        date: "2023-05-08",
        customer: "Nour Saleh",
        phone: "+961 81 678 901",
        pickupLocation: "Aley, Mount Lebanon",
        deliveryLocation: "Bhamdoun, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 89.99,
    },
    {
        id: "ORD-009",
        date: "2023-05-07",
        customer: "Fadi Karam",
        phone: "+961 3 789 012",
        pickupLocation: "Beit Mery, Mount Lebanon",
        deliveryLocation: "Broummana, Mount Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 42.75,
    },
    {
        id: "ORD-010",
        date: "2023-05-06",
        customer: "Rima Saad",
        phone: "+961 70 890 123",
        pickupLocation: "Jbeil, Mount Lebanon",
        deliveryLocation: "Amchit, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 112.25,
    },
    {
        id: "ORD-011",
        date: "2023-05-05",
        customer: "Walid Tawfiq",
        phone: "+961 71 901 234",
        pickupLocation: "Raouche, Beirut",
        deliveryLocation: "Ramlet El Baida, Beirut",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 67.50,
    },
    {
        id: "ORD-012",
        date: "2023-05-04",
        customer: "Yasmine Hariri",
        phone: "+961 76 012 345",
        pickupLocation: "Gemmayzeh, Beirut",
        deliveryLocation: "Mar Mikhael, Beirut",
        status: "processing",
        paymentStatus: "partial",
        amount: 95.25,
    },
    {
        id: "ORD-013",
        date: "2023-05-03",
        customer: "Bilal Farhat",
        phone: "+961 3 123 456",
        pickupLocation: "Antelias, Mount Lebanon",
        deliveryLocation: "Dbayeh, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 145.00,
    },
    {
        id: "ORD-014",
        date: "2023-05-02",
        customer: "Hiba Nassar",
        phone: "+961 78 234 567",
        pickupLocation: "Saida, South Lebanon",
        deliveryLocation: "Jezzine, South Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 55.75,
    },
    {
        id: "ORD-015",
        date: "2023-05-01",
        customer: "Omar Jaber",
        phone: "+961 79 345 678",
        pickupLocation: "Chouf, Mount Lebanon",
        deliveryLocation: "Baakline, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 78.50,
    },
    {
        id: "ORD-016",
        date: "2023-04-30",
        customer: "Lina Khalil",
        phone: "+961 81 456 789",
        pickupLocation: "Achrafieh, Beirut",
        deliveryLocation: "Gemmayze, Beirut",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 63.25,
    },
    {
        id: "ORD-017",
        date: "2023-04-29",
        customer: "Sami Haddad",
        phone: "+961 3 567 890",
        pickupLocation: "Hamra, Beirut",
        deliveryLocation: "Ras Beirut, Beirut",
        status: "processing",
        paymentStatus: "partial",
        amount: 105.75,
    },
    {
        id: "ORD-018",
        date: "2023-04-28",
        customer: "Dalia Moussa",
        phone: "+961 70 678 901",
        pickupLocation: "Baabda, Mount Lebanon",
        deliveryLocation: "Hadath, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 92.50,
    },
    {
        id: "ORD-019",
        date: "2023-04-27",
        customer: "Rami Aoun",
        phone: "+961 71 789 012",
        pickupLocation: "Byblos, Mount Lebanon",
        deliveryLocation: "Amchit, Mount Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 48.75,
    },
    {
        id: "ORD-020",
        date: "2023-04-26",
        customer: "Zeina Khoury",
        phone: "+961 76 890 123",
        pickupLocation: "Jounieh, Mount Lebanon",
        deliveryLocation: "Kaslik, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 115.25,
    },
    {
        id: "ORD-021",
        date: "2023-04-25",
        customer: "Marwan Saab",
        phone: "+961 3 901 234",
        pickupLocation: "Tripoli, North Lebanon",
        deliveryLocation: "Mina, North Lebanon",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 72.50,
    },
    {
        id: "ORD-022",
        date: "2023-04-24",
        customer: "Nadia Rizk",
        phone: "+961 78 012 345",
        pickupLocation: "Zahle, Bekaa Valley",
        deliveryLocation: "Chtaura, Bekaa Valley",
        status: "processing",
        paymentStatus: "partial",
        amount: 98.75,
    },
    {
        id: "ORD-023",
        date: "2023-04-23",
        customer: "Jad Salloum",
        phone: "+961 79 123 456",
        pickupLocation: "Sidon, South Lebanon",
        deliveryLocation: "Ghazieh, South Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 135.00,
    },
    {
        id: "ORD-024",
        date: "2023-04-22",
        customer: "Maya Abou Jaoude",
        phone: "+961 81 234 567",
        pickupLocation: "Verdun, Beirut",
        deliveryLocation: "Ain El Mreisseh, Beirut",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 59.25,
    },
    {
        id: "ORD-025",
        date: "2023-04-21",
        customer: "Elie Chamoun",
        phone: "+961 3 345 678",
        pickupLocation: "Jbeil, Mount Lebanon",
        deliveryLocation: "Fidar, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 82.50,
    },
    {
        id: "ORD-026",
        date: "2023-04-20",
        customer: "Rana Makarem",
        phone: "+961 70 456 789",
        pickupLocation: "Nabatieh, South Lebanon",
        deliveryLocation: "Kfar Roummane, South Lebanon",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 68.25,
    },
    {
        id: "ORD-027",
        date: "2023-04-19",
        customer: "Tarek Zein",
        phone: "+961 71 567 890",
        pickupLocation: "Aley, Mount Lebanon",
        deliveryLocation: "Souk El Gharb, Mount Lebanon",
        status: "processing",
        paymentStatus: "partial",
        amount: 110.75,
    },
    {
        id: "ORD-028",
        date: "2023-04-18",
        customer: "Carla Sfeir",
        phone: "+961 76 678 901",
        pickupLocation: "Achrafieh, Beirut",
        deliveryLocation: "Badaro, Beirut",
        status: "completed",
        paymentStatus: "paid",
        amount: 95.50,
    },
    {
        id: "ORD-029",
        date: "2023-04-17",
        customer: "Hadi Nasrallah",
        phone: "+961 3 789 012",
        pickupLocation: "Hamra, Beirut",
        deliveryLocation: "Clemenceau, Beirut",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 52.75,
    },
    {
        id: "ORD-030",
        date: "2023-04-16",
        customer: "Lamia Fakhry",
        phone: "+961 78 890 123",
        pickupLocation: "Jounieh, Mount Lebanon",
        deliveryLocation: "Zouk Mosbeh, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 118.25,
    }
];
