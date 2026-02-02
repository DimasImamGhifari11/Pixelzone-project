
describe('Fitur Pendaftaran Pengguna', () => {

  const generateUniqueEmail = () => {
    const uniqueId = Date.now();
    return `testuser_${uniqueId}@example.com`;
  };

  beforeEach(() => {
    cy.visit('/register');
  });

  it('TC-REGISTER-01: Berhasil mendaftarkan pengguna baru dengan data valid', () => {
    const userEmail = generateUniqueEmail();
    cy.intercept('POST', '**/api/register').as('registerRequest');
    cy.get('#name').type('Test User');
    cy.get('#email').type(userEmail);
    cy.get('#password').type('password123');
    cy.get('#password_confirmation').type('password123');
    cy.get('button[type="submit"]').click();
    cy.wait('@registerRequest').its('response.statusCode').should('eq', 201);
    cy.location('pathname').should('eq', '/login');
  });

  it('TC-REGISTER-02: Gagal mendaftar jika konfirmasi kata sandi tidak cocok', () => {
    cy.intercept('POST', '**/api/register').as('registerRequest');
    cy.get('#name').type('Test User');
    cy.get('#email').type(generateUniqueEmail());
    cy.get('#password').type('password123');
    cy.get('#password_confirmation').type('password-tidak-cocok'); 
    cy.get('button[type="submit"]').click();
    cy.wait('@registerRequest').its('response.statusCode').should('eq', 422);
    cy.get('.alert.alert-danger').should('be.visible').and('contain.text', 'Pendaftaran gagal');
    cy.location('pathname').should('eq', '/register');
  });

});