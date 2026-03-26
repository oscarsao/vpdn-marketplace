// ═══════════════════════════════════════════════════════════
// COYAG VPDN — Complete Mock Dataset for Local Development
// All data mirrors the Laravel API structure
// ═══════════════════════════════════════════════════════════

import { mockBusinesses } from './mockBusinesses'

// ── Users & Auth ─────────────────────────────────────────
export const mockUsers = {
  admin: {
    id: 1,
    names: 'Carlos',
    surnames: 'García López',
    email: 'admin@coyag.com',
    phone: '+34 600 100 100',
    avatar: null,
    roles: [{ id_role: 10, name: 'Administrador' }],
    created_at: '2024-06-15T10:00:00Z',
  },
  client: {
    id: 50,
    names: 'María',
    surnames: 'Rodríguez Sánchez',
    email: 'maria@example.com',
    phone: '+34 611 222 333',
    avatar: null,
    roles: [{ id_role: 50, name: 'Cliente' }],
    created_at: '2025-01-20T14:30:00Z',
  },
}

// ── Geographic Data (Spain) ──────────────────────────────
export const mockProvinces = [
  { id: 28, name: 'Madrid', autonomous_community_id: 13 },
  { id: 8, name: 'Barcelona', autonomous_community_id: 9 },
  { id: 46, name: 'Valencia', autonomous_community_id: 10 },
  { id: 41, name: 'Sevilla', autonomous_community_id: 1 },
  { id: 29, name: 'Málaga', autonomous_community_id: 1 },
  { id: 48, name: 'Vizcaya', autonomous_community_id: 16 },
  { id: 20, name: 'Guipúzcoa', autonomous_community_id: 16 },
  { id: 3, name: 'Alicante', autonomous_community_id: 10 },
]

export const mockMunicipalities = [
  { id: 1, name: 'Madrid', province_id: 28 },
  { id: 2, name: 'Getafe', province_id: 28 },
  { id: 3, name: 'Alcobendas', province_id: 28 },
  { id: 4, name: 'Alcalá de Henares', province_id: 28 },
  { id: 5, name: 'Leganés', province_id: 28 },
  { id: 6, name: 'Móstoles', province_id: 28 },
  { id: 7, name: 'Fuenlabrada', province_id: 28 },
  { id: 10, name: 'Barcelona', province_id: 8 },
  { id: 11, name: 'Hospitalet de Llobregat', province_id: 8 },
  { id: 20, name: 'Valencia', province_id: 46 },
  { id: 30, name: 'Sevilla', province_id: 41 },
  { id: 40, name: 'Málaga', province_id: 29 },
]

export const mockDistricts = [
  { id: 1, name: 'Centro', municipality_id: 1 },
  { id: 2, name: 'Arganzuela', municipality_id: 1 },
  { id: 3, name: 'Retiro', municipality_id: 1 },
  { id: 4, name: 'Salamanca', municipality_id: 1 },
  { id: 5, name: 'Chamartín', municipality_id: 1 },
  { id: 6, name: 'Tetuán', municipality_id: 1 },
  { id: 7, name: 'Chamberí', municipality_id: 1 },
  { id: 8, name: 'Fuencarral-El Pardo', municipality_id: 1 },
  { id: 9, name: 'Moncloa-Aravaca', municipality_id: 1 },
  { id: 10, name: 'Latina', municipality_id: 1 },
  { id: 11, name: 'Carabanchel', municipality_id: 1 },
  { id: 12, name: 'Usera', municipality_id: 1 },
  { id: 13, name: 'Puente de Vallecas', municipality_id: 1 },
  { id: 14, name: 'Moratalaz', municipality_id: 1 },
  { id: 15, name: 'Ciudad Lineal', municipality_id: 1 },
  { id: 16, name: 'Hortaleza', municipality_id: 1 },
  { id: 17, name: 'Villaverde', municipality_id: 1 },
  { id: 18, name: 'Villa de Vallecas', municipality_id: 1 },
  { id: 19, name: 'Vicálvaro', municipality_id: 1 },
  { id: 20, name: 'San Blas-Canillejas', municipality_id: 1 },
  { id: 21, name: 'Barajas', municipality_id: 1 },
  { id: 50, name: 'Norte', municipality_id: 2 },
  { id: 51, name: 'Sur', municipality_id: 2 },
]

export const mockNeighborhoods = [
  { id: 1, name: 'Sol', district_id: 1 },
  { id: 2, name: 'Huertas-Cortes', district_id: 1 },
  { id: 3, name: 'Malasaña', district_id: 1 },
  { id: 4, name: 'Chueca', district_id: 1 },
  { id: 5, name: 'Lavapiés', district_id: 1 },
  { id: 6, name: 'Embajadores', district_id: 1 },
  { id: 7, name: 'Universidad', district_id: 1 },
  { id: 8, name: 'La Latina', district_id: 1 },
  { id: 10, name: 'Imperial', district_id: 2 },
  { id: 11, name: 'Acacias', district_id: 2 },
  { id: 12, name: 'Delicias', district_id: 2 },
  { id: 20, name: 'Argüelles', district_id: 9 },
  { id: 21, name: 'Ciudad Universitaria', district_id: 9 },
  { id: 50, name: 'Los Ángeles', district_id: 50 },
]

// ── Business Sectors ─────────────────────────────────────
export const mockSectors = [
  { id: 1, name: 'Restauración y Hostelería', count: 45 },
  { id: 2, name: 'Alimentación', count: 23 },
  { id: 3, name: 'Estética y Belleza', count: 18 },
  { id: 4, name: 'Gimnasios y Deporte', count: 12 },
  { id: 5, name: 'Salud y Clínicas', count: 15 },
  { id: 6, name: 'Ocio Nocturno', count: 9 },
  { id: 7, name: 'Locales Vacíos', count: 34 },
  { id: 8, name: 'Logística e Industrial', count: 8 },
  { id: 9, name: 'Tecnología', count: 6 },
  { id: 10, name: 'Educación y Formación', count: 11 },
  { id: 11, name: 'Moda y Retail', count: 14 },
  { id: 12, name: 'Servicios Profesionales', count: 7 },
]

// ── Business Types ───────────────────────────────────────
export const mockBusinessTypes = [
  { id: 1, name: 'Traspaso', slug: 'traspaso' },
  { id: 2, name: 'Franquicia', slug: 'franquicia' },
  { id: 3, name: 'Inmueble', slug: 'inmueble' },
]

// ── Extended Business Data (20+ businesses) ──────────────
export const mockBusinessesFull = [
  ...mockBusinesses,
  {
    id: 93210,
    type: 'traspaso',
    title: 'Peluquería y Centro de Estética en Salamanca',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Salamanca', neighborhood: 'Recoletos', fullAddress: 'Calle de Serrano 42, Madrid' },
    financials: { price: 185000, rent: 2800, currency: '€' },
    features: { size: 95, bathrooms: 2, smokeOutlet: false, terrace: false, facade: '6 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 8, views: 420, favorites: 28, reference: 'REF-93210', source: 'VPDN Direct' },
    agent: { name: 'Beauty Invest Madrid', contact: 'Laura Fernández', phone: '+34 666 100 200' },
    sectors: ['Estética y Belleza', 'Peluquería'],
    images: ['https://images.unsplash.com/photo-1560066984-138dadb4c035?q=80&w=800&auto=format&fit=crop'],
    description: 'Peluquería y centro de estética de alto standing en el exclusivo barrio de Salamanca. Clientela fidelizada de más de 300 clientes activos.'
  },
  {
    id: 93211,
    type: 'traspaso',
    title: 'Pizzería Artesanal con Horno de Leña en Malasaña',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Centro', neighborhood: 'Malasaña', fullAddress: 'Calle de Velarde 15, Madrid' },
    financials: { price: 120000, rent: 1900, currency: '€' },
    features: { size: 80, bathrooms: 1, smokeOutlet: true, terrace: true, facade: '5 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 3, views: 680, favorites: 55, reference: 'REF-93211', source: 'Idealista' },
    agent: { name: 'GastroInvest', contact: 'Pedro Alonso', phone: '+34 666 200 300' },
    sectors: ['Restauración', 'Pizzería'],
    images: ['https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=800&auto=format&fit=crop'],
    description: 'Auténtica pizzería artesanal con horno de leña napolitano y terraza en la animada calle Velarde. Facturación mensual demostrable de 25.000€.'
  },
  {
    id: 93212,
    type: 'franquicia',
    title: 'Franquicia de Lavandería Autoservicio 24h',
    location: { province: 'Madrid', municipality: 'Alcobendas', district: 'Centro', neighborhood: 'Centro', fullAddress: 'Av. de España 20, Alcobendas' },
    financials: { price: 95000, rent: 1200, currency: '€' },
    features: { size: 60, bathrooms: 1, smokeOutlet: false, terrace: false, facade: '7 m', status: 'Llave en mano' },
    metadata: { daysOnMarket: 30, views: 95, favorites: 4, reference: 'REF-93212', source: 'VPDN Direct' },
    agent: { name: 'Clean24 Franquicias', contact: 'Isabel Ruiz', phone: '+34 666 300 400' },
    sectors: ['Servicios', 'Lavandería', 'Franquicia'],
    images: ['https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=800&auto=format&fit=crop'],
    description: 'Franquicia de lavandería autoservicio 24 horas. Modelo de negocio semi-pasivo con alta rentabilidad y mínima gestión diaria.'
  },
  {
    id: 93213,
    type: 'inmueble',
    title: 'Local Comercial en Esquina - Gran Vía',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Centro', neighborhood: 'Sol', fullAddress: 'Gran Vía 30, Madrid' },
    financials: { price: 1200000, rent: 8500, currency: '€' },
    features: { size: 200, bathrooms: 2, smokeOutlet: true, terrace: false, facade: '12 m', status: 'Vacío' },
    metadata: { daysOnMarket: 60, views: 1200, favorites: 78, reference: 'REF-93213', source: 'Fotocasa' },
    agent: { name: 'Gran Vía Real Estate', contact: 'Andrés Martínez', phone: '+34 666 400 500' },
    sectors: ['Local Comercial', 'Retail'],
    images: ['https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=800&auto=format&fit=crop'],
    description: 'Excepcional local comercial en esquina en la emblemática Gran Vía. Doble fachada con escaparates de gran visibilidad. Ideal para flagship store o restaurante premium.'
  },
  {
    id: 93214,
    type: 'traspaso',
    title: 'Taller Mecánico con ITV Propia',
    location: { province: 'Madrid', municipality: 'Leganés', district: 'Centro', neighborhood: 'La Fortuna', fullAddress: 'Polígono Butarque, Leganés' },
    financials: { price: 450000, rent: 3500, currency: '€' },
    features: { size: 600, bathrooms: 3, smokeOutlet: false, terrace: false, facade: '20 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 18, views: 180, favorites: 14, reference: 'REF-93214', source: 'VPDN Direct' },
    agent: { name: 'Auto Invest Madrid', contact: 'Roberto Díaz', phone: '+34 666 500 600' },
    sectors: ['Automoción', 'Taller'],
    images: ['https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?q=80&w=800&auto=format&fit=crop'],
    description: 'Taller mecánico multimarca con línea de ITV integrada. Licencia completa con 6 elevadores hidráulicos, sala de pintura y alineación 3D.'
  },
  {
    id: 93215,
    type: 'franquicia',
    title: 'Franquicia de Helados Artesanales Premium',
    location: { province: 'Barcelona', municipality: 'Barcelona', district: 'Eixample', neighborhood: 'Dreta Eixample', fullAddress: 'Passeig de Gràcia 85, Barcelona' },
    financials: { price: 210000, rent: 3800, currency: '€' },
    features: { size: 70, bathrooms: 1, smokeOutlet: false, terrace: true, facade: '6 m', status: 'Llave en mano' },
    metadata: { daysOnMarket: 10, views: 350, favorites: 22, reference: 'REF-93215', source: 'VPDN Direct' },
    agent: { name: 'Gelato Group', contact: 'Marta Solé', phone: '+34 666 600 700' },
    sectors: ['Alimentación', 'Helados', 'Franquicia'],
    images: ['https://images.unsplash.com/photo-1501443762994-82bd5dace89a?q=80&w=800&auto=format&fit=crop'],
    description: 'Franquicia premium de helados artesanales italianos en una de las calles más transitadas de Barcelona. Terraza con vistas a la Casa Batlló.'
  },
  {
    id: 93216,
    type: 'traspaso',
    title: 'Centro de Formación y Academia de Idiomas',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Chamberí', neighborhood: 'Trafalgar', fullAddress: 'Calle de Fuencarral 120, Madrid' },
    financials: { price: 160000, rent: 2200, currency: '€' },
    features: { size: 200, bathrooms: 3, smokeOutlet: false, terrace: false, facade: '8 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 25, views: 130, favorites: 9, reference: 'REF-93216', source: 'Idealista' },
    agent: { name: 'EduInvest', contact: 'Teresa Blanco', phone: '+34 666 700 800' },
    sectors: ['Educación', 'Formación', 'Idiomas'],
    images: ['https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800&auto=format&fit=crop'],
    description: 'Academia de idiomas con 5 aulas equipadas, sala multimedia y certificación Cambridge. Más de 200 alumnos matriculados.'
  },
  {
    id: 93217,
    type: 'inmueble',
    title: 'Oficina Premium Coworking-Ready en Azca',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Tetuán', neighborhood: 'Azca', fullAddress: 'Paseo de la Castellana 77, Madrid' },
    financials: { price: 980000, rent: 7200, currency: '€' },
    features: { size: 350, bathrooms: 4, smokeOutlet: false, terrace: true, facade: '15 m', status: 'Recién Reformado' },
    metadata: { daysOnMarket: 7, views: 520, favorites: 35, reference: 'REF-93217', source: 'VPDN Direct' },
    agent: { name: 'Corporate Space Madrid', contact: 'Álvaro Navarro', phone: '+34 666 800 900' },
    sectors: ['Oficinas', 'Coworking'],
    images: ['https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=800&auto=format&fit=crop'],
    description: 'Espectacular oficina de 350m² en pleno distrito financiero de Azca. Completamente reformada con diseño contemporáneo, terraza privada y vistas panorámicas.'
  },
  {
    id: 93218,
    type: 'traspaso',
    title: 'Tienda de Moda Multimarca en Chueca',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Centro', neighborhood: 'Chueca', fullAddress: 'Calle de Hortaleza 50, Madrid' },
    financials: { price: 95000, rent: 2100, currency: '€' },
    features: { size: 85, bathrooms: 1, smokeOutlet: false, terrace: false, facade: '5.5 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 20, views: 270, favorites: 16, reference: 'REF-93218', source: 'Idealista' },
    agent: { name: 'Fashion Point', contact: 'Lucía Herrero', phone: '+34 666 900 100' },
    sectors: ['Moda', 'Retail'],
    images: ['https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?q=80&w=800&auto=format&fit=crop'],
    description: 'Tienda de moda multimarca en el corazón de Chueca. Clientela fidelizada y presencia online consolidada con e-commerce activo.'
  },
  {
    id: 93219,
    type: 'traspaso',
    title: 'Farmacia con Parafarmacia en zona residencial',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Hortaleza', neighborhood: 'Pinar del Rey', fullAddress: 'Calle de Arturo Soria 200, Madrid' },
    financials: { price: 520000, rent: 2500, currency: '€' },
    features: { size: 130, bathrooms: 2, smokeOutlet: false, terrace: false, facade: '8 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 35, views: 310, favorites: 42, reference: 'REF-93219', source: 'VPDN Direct' },
    agent: { name: 'Pharma Invest', contact: 'Diego Moreno', phone: '+34 667 100 200' },
    sectors: ['Salud', 'Farmacia'],
    images: ['https://images.unsplash.com/photo-1587854692152-cbe660dbde88?q=80&w=800&auto=format&fit=crop'],
    description: 'Farmacia con licencia activa y sección de parafarmacia en zona residencial de alto poder adquisitivo. Facturación anual superior a 800.000€.'
  },
  // ── Franquicias ──
  {
    id: 93220,
    type: 'franquicia',
    title: 'Franquicia de Lavandería Autoservicio - Mr. Jeff',
    location: { province: 'Barcelona', municipality: 'Barcelona', district: 'Eixample', neighborhood: 'Dreta de l\'Eixample', fullAddress: 'Carrer de València 280, Barcelona' },
    financials: { price: 95000, rent: 1800, currency: '€' },
    features: { size: 60, bathrooms: 1, smokeOutlet: false, terrace: false, facade: '5 m', status: 'Llave en mano' },
    metadata: { daysOnMarket: 12, views: 280, favorites: 35, reference: 'REF-93220', source: 'VPDN Direct' },
    agent: { name: 'Franquicias BCN', contact: 'Marc Puig', phone: '+34 671 200 300' },
    sectors: ['Lavandería', 'Servicios'],
    images: ['https://images.unsplash.com/photo-1545173168-9f1947eebb7f?q=80&w=800&auto=format&fit=crop'],
    description: 'Franquicia de lavandería autoservicio con modelo probado. Incluye maquinaria industrial, formación completa y asistencia permanente de la central.'
  },
  {
    id: 93221,
    type: 'franquicia',
    title: 'Franquicia de Helados Artesanales - Amorino',
    location: { province: 'Valencia', municipality: 'Valencia', district: 'Ciutat Vella', neighborhood: 'El Carmen', fullAddress: 'Carrer de la Pau 12, Valencia' },
    financials: { price: 180000, rent: 3200, currency: '€' },
    features: { size: 45, bathrooms: 1, smokeOutlet: false, terrace: true, facade: '4 m', status: 'Equipado' },
    metadata: { daysOnMarket: 20, views: 450, favorites: 52, reference: 'REF-93221', source: 'Franquicia Directa' },
    agent: { name: 'Amorino España', contact: 'Pilar Ruiz', phone: '+34 672 300 400' },
    sectors: ['Helados', 'Alimentación'],
    images: ['https://images.unsplash.com/photo-1501443762994-82bd5dace89a?q=80&w=800&auto=format&fit=crop'],
    description: 'Franquicia premium de helados artesanales italianos. Ubicación turística de primer nivel con alta afluencia durante todo el año.'
  },
  {
    id: 93222,
    type: 'franquicia',
    title: 'Franquicia de Café Specialty - The Coffee House',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Chamberí', neighborhood: 'Trafalgar', fullAddress: 'Calle de Fuencarral 78, Madrid' },
    financials: { price: 145000, rent: 2600, currency: '€' },
    features: { size: 55, bathrooms: 1, smokeOutlet: true, terrace: true, facade: '6 m', status: 'Llave en mano' },
    metadata: { daysOnMarket: 5, views: 620, favorites: 68, reference: 'REF-93222', source: 'VPDN Direct' },
    agent: { name: 'Coffee Franchising SL', contact: 'Alonso Vega', phone: '+34 673 400 500' },
    sectors: ['Cafetería', 'Restauración'],
    images: ['https://images.unsplash.com/photo-1559925393-8be0ec4767c8?q=80&w=800&auto=format&fit=crop'],
    description: 'Franquicia de cafetería specialty con marca reconocida. Terraza en plena calle Fuencarral. Facturación prevista de 25.000€/mes desde el primer año.'
  },
  {
    id: 93223,
    type: 'franquicia',
    title: 'Franquicia de Coworking - WeWork Flex',
    location: { province: 'Barcelona', municipality: 'Barcelona', district: 'Sant Martí', neighborhood: '22@', fullAddress: 'Carrer de Pallars 193, Barcelona' },
    financials: { price: 324000, rent: 5500, currency: '€' },
    features: { size: 280, bathrooms: 4, smokeOutlet: false, terrace: true, facade: '12 m', status: 'Recién Reformado' },
    metadata: { daysOnMarket: 15, views: 380, favorites: 45, reference: 'REF-93223', source: 'VPDN Direct' },
    agent: { name: 'Workspace Invest', contact: 'Ariadna Coll', phone: '+34 674 500 600' },
    sectors: ['Coworking', 'Oficinas'],
    images: ['https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=800&auto=format&fit=crop'],
    description: 'Espacio de coworking en el distrito tecnológico 22@ de Barcelona. 50 puestos, 4 salas de reuniones, terraza comunitaria. Ocupación actual del 85%.'
  },
  // ── Inmuebles ──
  {
    id: 93224,
    type: 'inmueble',
    title: 'Local Comercial a Estrenar en Sevilla Centro',
    location: { province: 'Sevilla', municipality: 'Sevilla', district: 'Casco Antiguo', neighborhood: 'Santa Cruz', fullAddress: 'Calle Sierpes 45, Sevilla' },
    financials: { price: 420000, rent: 3500, currency: '€' },
    features: { size: 150, bathrooms: 2, smokeOutlet: true, terrace: false, facade: '8 m', status: 'Vacío' },
    metadata: { daysOnMarket: 45, views: 210, favorites: 18, reference: 'REF-93224', source: 'VPDN Direct' },
    agent: { name: 'Sevilla Inmobiliaria', contact: 'Fernando Ortiz', phone: '+34 675 600 700' },
    sectors: ['Local Comercial', 'Retail'],
    images: ['https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?q=80&w=800&auto=format&fit=crop'],
    description: 'Local comercial de nueva construcción en la arteria comercial más importante de Sevilla. Listo para cualquier actividad.'
  },
  {
    id: 93225,
    type: 'inmueble',
    title: 'Nave Industrial en Polígono Getafe',
    location: { province: 'Madrid', municipality: 'Getafe', district: 'Industrial', neighborhood: 'Los Ángeles', fullAddress: 'Polígono Industrial Los Ángeles, Nave 14, Getafe' },
    financials: { price: 780000, rent: 4200, currency: '€' },
    features: { size: 650, bathrooms: 3, smokeOutlet: true, terrace: false, facade: '15 m', status: 'Equipado' },
    metadata: { daysOnMarket: 60, views: 180, favorites: 12, reference: 'REF-93225', source: 'Inmobiliaria Industrial' },
    agent: { name: 'Industrial Madrid SL', contact: 'Roberto Díaz', phone: '+34 676 700 800' },
    sectors: ['Industrial', 'Logística'],
    images: ['https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=800&auto=format&fit=crop'],
    description: 'Nave industrial de 650m2 con oficinas, zona de carga/descarga y aparcamiento propio. Perfecta para logística o producción.'
  },
  {
    id: 93226,
    type: 'inmueble',
    title: 'Local en Primera Línea de Playa - Málaga',
    location: { province: 'Málaga', municipality: 'Málaga', district: 'Este', neighborhood: 'Pedregalejo', fullAddress: 'Paseo Marítimo de Pedregalejo 32, Málaga' },
    financials: { price: 350000, rent: 3800, currency: '€' },
    features: { size: 120, bathrooms: 2, smokeOutlet: true, terrace: true, facade: '10 m', status: 'Vacío' },
    metadata: { daysOnMarket: 25, views: 520, favorites: 60, reference: 'REF-93226', source: 'VPDN Direct' },
    agent: { name: 'Costa del Sol Invest', contact: 'Antonio Morales', phone: '+34 677 800 900' },
    sectors: ['Local Comercial', 'Restauración y Hostelería'],
    images: ['https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800&auto=format&fit=crop'],
    description: 'Local en primera línea de playa ideal para restaurante o chiringuito. Terraza con vistas al mar. Alta temporada de abril a octubre.'
  },
  {
    id: 93227,
    type: 'inmueble',
    title: 'Oficinas Modernas en Torre Empresarial Valencia',
    location: { province: 'Valencia', municipality: 'Valencia', district: 'Ensanche', neighborhood: 'Gran Vía', fullAddress: 'Avenida del Marqués del Turia 60, Valencia' },
    financials: { price: 290000, rent: 2200, currency: '€' },
    features: { size: 180, bathrooms: 3, smokeOutlet: false, terrace: false, facade: '0 m', status: 'Recién Reformado' },
    metadata: { daysOnMarket: 18, views: 260, favorites: 22, reference: 'REF-93227', source: 'VPDN Direct' },
    agent: { name: 'Valencia Business', contact: 'Isabel Navarro', phone: '+34 678 900 100' },
    sectors: ['Oficinas', 'Servicios Profesionales'],
    images: ['https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=800&auto=format&fit=crop'],
    description: 'Oficinas de diseño en planta 5 con vistas panorámicas. Climatización centralizada, fibra óptica, control de accesos. Ideal para consultora o tech.'
  },
  // ── Más traspasos variados ──
  {
    id: 93228,
    type: 'traspaso',
    title: 'Taller Mecánico con ITV en Alcobendas',
    location: { province: 'Madrid', municipality: 'Alcobendas', district: 'Centro', neighborhood: 'Centro', fullAddress: 'Calle de la Industria 12, Alcobendas' },
    financials: { price: 210000, rent: 2000, currency: '€' },
    features: { size: 350, bathrooms: 2, smokeOutlet: true, terrace: false, facade: '12 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 40, views: 150, favorites: 15, reference: 'REF-93228', source: 'VPDN Direct' },
    agent: { name: 'AutoNegocios SL', contact: 'Raúl Gómez', phone: '+34 679 100 200' },
    sectors: ['Automoción', 'Taller'],
    images: ['https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?q=80&w=800&auto=format&fit=crop'],
    description: 'Taller mecánico con línea de ITV propia. 6 puestos de trabajo, sala de espera y tienda de recambios. Clientela fiel de más de 15 años.'
  },
  {
    id: 93229,
    type: 'traspaso',
    title: 'Academia de Idiomas en Barcelona Eixample',
    location: { province: 'Barcelona', municipality: 'Barcelona', district: 'Eixample', neighborhood: 'Esquerra de l\'Eixample', fullAddress: 'Carrer d\'Aragó 315, Barcelona' },
    financials: { price: 125000, rent: 2400, currency: '€' },
    features: { size: 200, bathrooms: 3, smokeOutlet: false, terrace: false, facade: '7 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 30, views: 190, favorites: 20, reference: 'REF-93229', source: 'VPDN Direct' },
    agent: { name: 'Edu Invest BCN', contact: 'Nuria Soler', phone: '+34 680 200 300' },
    sectors: ['Educación', 'Idiomas'],
    images: ['https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800&auto=format&fit=crop'],
    description: 'Academia de idiomas con 8 aulas, sala multimedia y biblioteca. 350 alumnos matriculados entre inglés, francés, alemán y chino.'
  },
  {
    id: 93230,
    type: 'traspaso',
    title: 'Clínica Dental en Chamartín',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Chamartín', neighborhood: 'Hispanoamérica', fullAddress: 'Calle del Príncipe de Vergara 210, Madrid' },
    financials: { price: 490000, rent: 3200, currency: '€' },
    features: { size: 160, bathrooms: 3, smokeOutlet: false, terrace: false, facade: '6 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 10, views: 340, favorites: 38, reference: 'REF-93230', source: 'VPDN Direct' },
    agent: { name: 'Salud Business', contact: 'Dr. Enrique Ruiz', phone: '+34 681 300 400' },
    sectors: ['Salud', 'Clínica'],
    images: ['https://images.unsplash.com/photo-1629909613654-28e377c37b09?q=80&w=800&auto=format&fit=crop'],
    description: 'Clínica dental premium con 4 gabinetes, ortopanorámica digital y sala de cirugía. Cartera de 2.000+ pacientes activos. Facturación 600K€/año.'
  },
  {
    id: 93231,
    type: 'traspaso',
    title: 'Tienda de Moda Sostenible en Gracia, Barcelona',
    location: { province: 'Barcelona', municipality: 'Barcelona', district: 'Gràcia', neighborhood: 'Vila de Gràcia', fullAddress: 'Carrer de Verdi 38, Barcelona' },
    financials: { price: 78000, rent: 1500, currency: '€' },
    features: { size: 70, bathrooms: 1, smokeOutlet: false, terrace: false, facade: '5 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 22, views: 240, favorites: 30, reference: 'REF-93231', source: 'VPDN Direct' },
    agent: { name: 'Fashion Business BCN', contact: 'Clara Bonet', phone: '+34 682 400 500' },
    sectors: ['Moda', 'Retail'],
    images: ['https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=800&auto=format&fit=crop'],
    description: 'Tienda de moda sostenible y ecológica en el trendy barrio de Gràcia. E-commerce integrado con 5.000 seguidores. Margen bruto del 55%.'
  },
  {
    id: 93232,
    type: 'traspaso',
    title: 'Cervecería Craft con Cocina en Ruzafa, Valencia',
    location: { province: 'Valencia', municipality: 'Valencia', district: 'Ensanche', neighborhood: 'Ruzafa', fullAddress: 'Calle de Sueca 28, Valencia' },
    financials: { price: 110000, rent: 1900, currency: '€' },
    features: { size: 100, bathrooms: 2, smokeOutlet: true, terrace: true, facade: '7 m', status: 'En funcionamiento' },
    metadata: { daysOnMarket: 14, views: 380, favorites: 45, reference: 'REF-93232', source: 'VPDN Direct' },
    agent: { name: 'Hostelería Valencia', contact: 'Pablo Serrano', phone: '+34 683 500 600' },
    sectors: ['Restauración y Hostelería', 'Bar'],
    images: ['https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=800&auto=format&fit=crop'],
    description: 'Cervecería artesanal con cocina de autor en el barrio más de moda de Valencia. 16 grifos de cerveza craft, terraza y clientela joven fidelizada.'
  },
  {
    id: 93233,
    type: 'inmueble',
    title: 'Local esquina Gran Vía de Madrid',
    location: { province: 'Madrid', municipality: 'Madrid', district: 'Centro', neighborhood: 'Sol', fullAddress: 'Gran Vía 42, Madrid' },
    financials: { price: 1200000, rent: 8500, currency: '€' },
    features: { size: 220, bathrooms: 3, smokeOutlet: true, terrace: false, facade: '14 m', status: 'Vacío' },
    metadata: { daysOnMarket: 90, views: 850, favorites: 95, reference: 'REF-93233', source: 'Premium Listing' },
    agent: { name: 'Gran Vía Properties', contact: 'Alberto Moreno', phone: '+34 684 600 700' },
    sectors: ['Local Comercial', 'Retail'],
    images: ['https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=800&auto=format&fit=crop'],
    description: 'Local emblemático en esquina de Gran Vía con doble fachada de 14m. Ideal para flagship store, restaurante premium o marca internacional. Máxima visibilidad.'
  },
]

// ── Clients ──────────────────────────────────────────────
export const mockClients = [
  { id: 50, names: 'María', surnames: 'Rodríguez Sánchez', email: 'maria@example.com', phone: '+34 611 222 333', plan: 'Premium', status: 'Activo', created_at: '2025-01-20' },
  { id: 51, names: 'Juan', surnames: 'Pérez Martín', email: 'juan@example.com', phone: '+34 622 333 444', plan: 'Básico', status: 'Activo', created_at: '2025-02-10' },
  { id: 52, names: 'Ana', surnames: 'López Díaz', email: 'ana@example.com', phone: '+34 633 444 555', plan: 'Premium', status: 'Activo', created_at: '2025-01-05' },
  { id: 53, names: 'Pedro', surnames: 'Gómez Torres', email: 'pedro@example.com', phone: '+34 644 555 666', plan: 'Básico', status: 'Inactivo', created_at: '2024-11-18' },
  { id: 54, names: 'Laura', surnames: 'Fernández Gil', email: 'laura@example.com', phone: '+34 655 666 777', plan: 'Enterprise', status: 'Activo', created_at: '2024-12-01' },
  { id: 55, names: 'Carlos', surnames: 'Martín Ruiz', email: 'carlos@example.com', phone: '+34 666 777 888', plan: 'Premium', status: 'Activo', created_at: '2025-03-01' },
  { id: 56, names: 'Elena', surnames: 'Navarro Blanco', email: 'elena@example.com', phone: '+34 677 888 999', plan: 'Básico', status: 'Activo', created_at: '2025-02-28' },
  { id: 57, names: 'Roberto', surnames: 'Díaz Serrano', email: 'roberto@example.com', phone: '+34 688 999 000', plan: 'Premium', status: 'Inactivo', created_at: '2024-10-15' },
]

// ── Employees ────────────────────────────────────────────
export const mockEmployees = [
  { id: 1, names: 'Carlos', surnames: 'García López', email: 'admin@coyag.com', role: 'Administrador', department: 'Dirección', status: 'Activo', id_role: 10 },
  { id: 2, names: 'Sofía', surnames: 'Martínez Vega', email: 'sofia@coyag.com', role: 'Asesora Comercial', department: 'Ventas', status: 'Activo', id_role: 30 },
  { id: 3, names: 'Miguel', surnames: 'Hernández Ríos', email: 'miguel@coyag.com', role: 'Asesor Senior', department: 'Ventas', status: 'Activo', id_role: 25 },
  { id: 4, names: 'Lucía', surnames: 'Sánchez Pérez', email: 'lucia@coyag.com', role: 'Asesora Inmobiliaria', department: 'Inmuebles', status: 'Activo', id_role: 30 },
  { id: 5, names: 'David', surnames: 'Fernández Ramos', email: 'david@coyag.com', role: 'Soporte Técnico', department: 'Tecnología', status: 'Activo', id_role: 35 },
]

// ── Calendar Events ──────────────────────────────────────
export const mockCalendarEvents = [
  { id: 1, title: 'Visita Local - Restaurante Embajadores', start: '2026-03-17T10:00:00', end: '2026-03-17T11:30:00', color: '#A40E05', client_id: 50, employee_id: 2, type: 'visita' },
  { id: 2, title: 'Reunión con Franquiciador Bakery', start: '2026-03-17T14:00:00', end: '2026-03-17T15:00:00', color: '#28C76F', client_id: 54, employee_id: 3, type: 'reunion' },
  { id: 3, title: 'Asesoría telefónica - Juan Pérez', start: '2026-03-18T09:30:00', end: '2026-03-18T10:00:00', color: '#7367F0', client_id: 51, employee_id: 2, type: 'llamada' },
  { id: 4, title: 'Cierre traspaso - Gym Boutique', start: '2026-03-19T12:00:00', end: '2026-03-19T13:30:00', color: '#FF9F43', client_id: 52, employee_id: 3, type: 'cierre' },
  { id: 5, title: 'Valoración Clínica Dental', start: '2026-03-20T16:00:00', end: '2026-03-20T17:00:00', color: '#A40E05', client_id: 55, employee_id: 4, type: 'valoracion' },
  { id: 6, title: 'Formación equipo - Nuevos procesos', start: '2026-03-21T11:00:00', end: '2026-03-21T13:00:00', color: '#EA5455', employee_id: 1, type: 'interna' },
]

// ── Notifications ────────────────────────────────────────
export const mockNotifications = [
  { id: 1, title: 'Nuevo lead recibido', message: 'María Rodríguez ha solicitado información sobre REF-93203', type: 'lead', read: false, created_at: '2026-03-17T08:30:00Z' },
  { id: 2, title: 'Negocio actualizado', message: 'El precio del gimnasio boutique REF-93200 ha sido modificado', type: 'business', read: false, created_at: '2026-03-16T15:00:00Z' },
  { id: 3, title: 'Cita programada', message: 'Visita al local de Embajadores mañana a las 10:00', type: 'calendar', read: false, created_at: '2026-03-16T12:00:00Z' },
  { id: 4, title: 'Nuevo cliente registrado', message: 'Carlos Martín se ha registrado como cliente Premium', type: 'client', read: true, created_at: '2026-03-15T18:00:00Z' },
  { id: 5, title: 'Informe semanal disponible', message: 'Las estadísticas de la semana 11 están disponibles', type: 'stats', read: true, created_at: '2026-03-14T09:00:00Z' },
]

// ── Statistics ────────────────────────────────────────────
export const mockStatistics = {
  totalBusinesses: 156,
  activeBusinesses: 124,
  soldBusinesses: 32,
  totalClients: 89,
  activeClients: 72,
  premiumClients: 34,
  totalLeads: 215,
  conversionRate: 18.5,
  avgDaysOnMarket: 28,
  avgPrice: 287000,
  monthlyRevenue: [42000, 38000, 51000, 47000, 55000, 62000, 58000, 71000, 65000, 78000, 82000, 89000],
  businessesByType: { traspaso: 78, franquicia: 35, inmueble: 43 },
  businessesBySector: [
    { name: 'Restauración', count: 45 },
    { name: 'Locales Vacíos', count: 34 },
    { name: 'Alimentación', count: 23 },
    { name: 'Estética', count: 18 },
    { name: 'Salud', count: 15 },
    { name: 'Moda', count: 14 },
    { name: 'Deporte', count: 12 },
    { name: 'Educación', count: 11 },
  ],
  clientsByPlan: { basico: 35, premium: 34, enterprise: 3 },
  monthLabels: ['Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar'],
  priceDistribution: [
    { range: '0-50k', count: 12 },
    { range: '50-100k', count: 28 },
    { range: '100-200k', count: 35 },
    { range: '200-500k', count: 32 },
    { range: '500k-1M', count: 14 },
    { range: '1M+', count: 3 },
  ],
}

// ── Favorites ────────────────────────────────────────────
export const mockFavoriteIds = [93196, 93203, 93199, 93211, 93222, 93226]

// ── Recommendations ──────────────────────────────────────
export const mockRecommendationIds = [93203, 93211, 93215, 93222, 93226, 93232]

// ── User Preferences ────────────────────────────────────
export const mockPreferences = {
  operation_types: ['traspaso', 'franquicia'],
  min_investment: 50000,
  max_investment: 300000,
  min_rental: 0,
  max_rental: 4000,
  provinces: [28],
  sectors: [1, 2, 4],
  features: { smokeOutlet: false, terrace: true },
}

// ── Assigned Advisor ─────────────────────────────────────
export const mockAdvisor = {
  id: 2,
  names: 'Sofía',
  surnames: 'Martínez Vega',
  email: 'sofia@coyag.com',
  phone: '+34 611 100 200',
  department: 'Ventas',
  specialization: 'Restauración y Hostelería',
  photo: null,
  availability: 'Lunes a Viernes, 9:00 - 18:00',
  rating: 4.8,
  totalClients: 23,
  closedDeals: 47,
}

// ── Client Requests / Solicitudes ────────────────────────
export const mockClientRequests = [
  { id: 1, type: 'info', business_ref: 'REF-93203', status: 'pendiente', message: 'Me gustaría agendar una visita al local', created_at: '2026-03-15T10:00:00Z' },
  { id: 2, type: 'videocall', business_ref: 'REF-93198', status: 'confirmada', message: 'Videollamada para ver documentación', created_at: '2026-03-12T14:30:00Z', scheduled_at: '2026-03-19T11:00:00Z' },
  { id: 3, type: 'valoracion', business_ref: null, status: 'completada', message: 'Valoración de mi propio negocio para traspaso', created_at: '2026-03-05T09:00:00Z' },
]

// ── Activity Feed ────────────────────────────────────────
export const mockActivityFeed = [
  { id: 1, action: 'view', description: 'Visitaste Bar de copas, discoteca (REF-93196)', timestamp: '2026-03-17T08:15:00Z' },
  { id: 2, action: 'favorite', description: 'Añadiste Restaurante premium a favoritos', timestamp: '2026-03-16T17:30:00Z' },
  { id: 3, action: 'compare', description: 'Comparaste 3 negocios del sector hostelería', timestamp: '2026-03-16T14:00:00Z' },
  { id: 4, action: 'request', description: 'Solicitaste información sobre Franquicia Bakery', timestamp: '2026-03-15T10:00:00Z' },
  { id: 5, action: 'login', description: 'Iniciaste sesión desde Madrid, España', timestamp: '2026-03-14T09:00:00Z' },
]

// ── Leads (Admin) ────────────────────────────────────────
export const mockLeads = [
  { id: 1, name: 'Francisco Almeida', email: 'falmeida@gmail.com', phone: '+34 699 111 222', business_ref: 'REF-93203', source: 'Web', status: 'nuevo', notes: 'Interesado en restaurante, presupuesto 300k€', created_at: '2026-03-17T07:45:00Z' },
  { id: 2, name: 'Sara Molina', email: 'sara.molina@outlook.es', phone: '+34 699 333 444', business_ref: 'REF-93198', source: 'Idealista', status: 'contactado', notes: 'Busca franquicia de café, experiencia previa en hostelería', created_at: '2026-03-16T16:20:00Z' },
  { id: 3, name: 'Ahmed El Khouri', email: 'ahmed.k@yahoo.com', phone: '+34 699 555 666', business_ref: null, source: 'Referido', status: 'cualificado', notes: 'Inversor con 500k€, busca varios locales en Madrid centro', created_at: '2026-03-15T11:00:00Z' },
  { id: 4, name: 'Claudia Bertoni', email: 'claudia.b@gmail.com', phone: '+34 699 777 888', business_ref: 'REF-93200', source: 'Web', status: 'negociando', notes: 'Interesada en el gimnasio, quiere negociar precio', created_at: '2026-03-14T09:30:00Z' },
  { id: 5, name: 'Jorge Ramírez', email: 'jramirez@hotmail.com', phone: '+34 699 999 000', business_ref: 'REF-93250', source: 'Fotocasa', status: 'cerrado', notes: 'Cerrado - Nave industrial Getafe', created_at: '2026-03-10T14:00:00Z' },
]
