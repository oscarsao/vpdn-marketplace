// Realistic mock data for the Hyper-Premium Data Density UI
export const mockBusinesses = [
  {
    id: 93196,
    type: 'traspaso',
    title: 'Bar de copas, discoteca en traspaso en Calle del Infante',
    location: {
      province: 'Madrid',
      municipality: 'Madrid',
      district: 'Centro',
      neighborhood: 'Huertas-cortes',
      fullAddress: 'Calle del Infante, Madrid Capital'
    },
    financials: {
      price: 65000,
      rent: 1600,
      currency: '€'
    },
    features: {
      size: 120, // m2
      bathrooms: 2,
      smokeOutlet: false,
      terrace: false,
      facade: '5 m',
      status: 'En funcionamiento'
    },
    metadata: {
      daysOnMarket: 14,
      views: 342,
      favorites: 12,
      reference: 'REF-93196',
      source: 'Idealista'
    },
    agent: {
      name: 'InmoRest Consultores S.L.',
      contact: 'Sebastian Hofmann',
      phone: '+34 600 000 000'
    },
    sectors: ['Ocio nocturno', 'Bar', 'Discoteca'],
    images: [
      'https://images.unsplash.com/photo-1572116469696-31de0f17cc34?q=80&w=800&auto=format&fit=crop', // neon bar
      'https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=800&auto=format&fit=crop'
    ],
    description: 'InmoRest consultores, S.L. traspasa una gran oportunidad para montar un bar especial con actuaciones en directo en la vibrante zona del centro de Madrid. Este local ofrece un gran ambiente, con una distribución en una sola planta que incluye una zona de barra, mesas tipo lounge, una pista de baile y al fondo una bodega...'
  },
  {
    id: 93203,
    type: 'traspaso',
    title: 'Local Comercial Restaurante en Calle de la Colegiata',
    location: {
      province: 'Madrid',
      municipality: 'Madrid',
      district: 'Centro',
      neighborhood: 'Embajadores',
      fullAddress: 'Calle de la Colegiata, Madrid Capital'
    },
    financials: {
      price: 270000,
      rent: 3200,
      currency: '€'
    },
    features: {
      size: 240, // m2
      bathrooms: 3,
      smokeOutlet: true,
      terrace: true,
      facade: '12 m',
      status: 'Recién Reformado'
    },
    metadata: {
      daysOnMarket: 4,
      views: 890,
      favorites: 45,
      reference: 'REF-93203',
      source: 'Fotocasa'
    },
    agent: {
      name: 'VPDN Premium',
      contact: 'María Torres',
      phone: '+34 611 111 111'
    },
    sectors: ['Restauración', 'Gastronomía'],
    images: [
      'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=800&auto=format&fit=crop', // fine dining
      'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=800&auto=format&fit=crop'
    ],
    description: 'Espectacular restaurante premium en el corazón de Embajadores. Totalmente equipado con cocina industrial de alta capacidad, salida de humos homologada y amplia terraza exterior. Licencia definitiva de hostelería.'
  },
  {
    id: 93198,
    type: 'franquicia',
    title: 'Franquicia Bakery & Coffee en zona Prime',
    location: {
      province: 'Madrid',
      municipality: 'Madrid',
      district: 'Centro',
      neighborhood: 'Universidad',
      fullAddress: 'Calle de la Princesa, Madrid Capital'
    },
    financials: {
      price: 324800,
      rent: 4500,
      currency: '€'
    },
    features: {
      size: 150, // m2
      bathrooms: 2,
      smokeOutlet: false,
      terrace: true,
      facade: '8 m',
      status: 'Llave en mano'
    },
    metadata: {
      daysOnMarket: 22,
      views: 150,
      favorites: 8,
      reference: 'REF-93198',
      source: 'VPDN Direct'
    },
    agent: {
      name: 'VPDN Franquicias',
      contact: 'Carlos Ruiz',
      phone: '+34 622 222 222'
    },
    sectors: ['Panadería', 'Cafetería', 'Franquicia'],
    images: [
      'https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=800&auto=format&fit=crop', // cozy cafe
      'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=800&auto=format&fit=crop'
    ],
    description: 'Oportunidad de inversión en franquicia de cafetería y panadería artesanal. Modelo de negocio probado con alta rentabilidad, ubicado en una zona de intenso tránsito peatonal y comercial.'
  },
  {
    id: 93250,
    type: 'inmueble',
    title: 'Nave Industrial Logística en Polígono',
    location: {
      province: 'Madrid',
      municipality: 'Getafe',
      district: 'Norte',
      neighborhood: 'Los Ángeles',
      fullAddress: 'Polígono Industrial Los Ángeles, Getafe'
    },
    financials: {
      price: 850000,
      rent: 0, // Venta pura
      currency: '€'
    },
    features: {
      size: 1200, // m2
      bathrooms: 4,
      smokeOutlet: false,
      terrace: false,
      facade: '30 m',
      status: 'Vacío'
    },
    metadata: {
      daysOnMarket: 45,
      views: 65,
      favorites: 2,
      reference: 'REF-93250',
      source: 'Idealista'
    },
    agent: {
      name: 'LogisMadrid RE',
      contact: 'Ana Silva',
      phone: '+34 633 333 333'
    },
    sectors: ['Logística', 'Industrial'],
    images: [
      'https://images.unsplash.com/photo-1586528116311-ad8ed7c83a75?q=80&w=800&auto=format&fit=crop', // warehouse
      'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=800&auto=format&fit=crop'
    ],
    description: 'Excelente nave industrial tipo C, diáfana, con 4 muelles de carga, oficinas en entreplanta, sistema contra incendios y amplia playa de maniobras para tráilers.'
  },
  {
    id: 93199,
    type: 'traspaso',
    title: 'Clínica Dental Moderna Totalmente Equipada',
    location: {
      province: 'Madrid',
      municipality: 'Madrid',
      district: 'Moncloa-Aravaca',
      neighborhood: 'Argüelles',
      fullAddress: 'Paseo del Pintor Rosales, Madrid'
    },
    financials: {
      price: 274000,
      rent: 2100,
      currency: '€'
    },
    features: {
      size: 180, // m2
      bathrooms: 2,
      smokeOutlet: false,
      terrace: false,
      facade: '10 m',
      status: 'Equipado'
    },
    metadata: {
      daysOnMarket: 12,
      views: 210,
      favorites: 18,
      reference: 'REF-93199',
      source: 'VPDN Direct'
    },
    agent: {
      name: 'Salud Invest',
      contact: 'Elena Gómez',
      phone: '+34 644 444 444'
    },
    sectors: ['Salud', 'Clínica'],
    images: [
      'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?q=80&w=800&auto=format&fit=crop', // modern clinic
      'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?q=80&w=800&auto=format&fit=crop'
    ],
    description: 'Traspaso de clínica dental de alto standing en Paseo del Pintor Rosales. Consta de 3 gabinetes completamente equipados con sillones de última generación, sala de rayos X (ortopantomógrafo), sala de esterilización y recepción elegante. Amplia cartera de clientes activos y facturación demostrable.'
  },
  {
    id: 93200,
    type: 'traspaso',
    title: 'Gimnasio Boutique y Centro de Entrenamiento Personal',
    location: {
      province: 'Madrid',
      municipality: 'Madrid',
      district: 'Arganzuela',
      neighborhood: 'Imperial',
      fullAddress: 'Paseo Imperial 1, Madrid'
    },
    financials: {
      price: 380000,
      rent: 4200,
      currency: '€'
    },
    features: {
      size: 450, // m2
      bathrooms: 4, // Including locker rooms
      smokeOutlet: false,
      terrace: false,
      facade: '15 m',
      status: 'En funcionamiento'
    },
    metadata: {
      daysOnMarket: 5,
      views: 540,
      favorites: 32,
      reference: 'REF-93200',
      source: 'VPDN Direct'
    },
    agent: {
      name: 'VPDN Deportes',
      contact: 'Javier Martín',
      phone: '+34 655 555 555'
    },
    sectors: ['Deporte', 'Gimnasio', 'Salud'],
    images: [
      'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?q=80&w=800&auto=format&fit=crop', // gym weights
      'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=800&auto=format&fit=crop'
    ],
    description: 'Exclusivo centro de entrenamiento personal y gimnasio boutique en zona prime de Arganzuela. Instalaciones premium con maquinaria Technogym, zonas de entrenamiento funcional, vestuarios de lujo y sala de fisioterapia. Gran potencial de crecimiento.'
  }
]
