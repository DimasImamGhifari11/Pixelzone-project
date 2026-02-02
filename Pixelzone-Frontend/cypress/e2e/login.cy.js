describe('Fitur Login Pengguna', () => {

  beforeEach(() => {
    cy.visit('/login');
  });

  it('TC-LOGIN-01: Berhasil login dengan kredensial yang valid', () => {
    cy.intercept('POST', '**/api/login').as('loginRequest');
    cy.get('input[type="email"]').type('imamghifaridimas@gmail.com');
    cy.get('input[type="password"]').type('12345678');
    cy.get('button[type="submit"]').click();
    cy.wait('@loginRequest').its('response.statusCode').should('eq', 200);
    cy.location('pathname').should('eq', '/dashboard');
  });

  it('TC-LOGIN-02: Gagal login dengan kredensial yang salah', () => {
    cy.intercept('POST', '**/api/login').as('loginRequest');
    cy.get('input[type="email"]').type('imamghifaridimas@gmail.com');
    cy.get('input[type="password"]').type('password-salah'); 
    cy.get('button[type="submit"]').click();
    cy.wait('@loginRequest').its('response.statusCode').should('be.oneOf', [401, 422]);
    cy.get('.alert.alert-danger').should('be.visible').and('contain.text', 'Gagal masuk');
    cy.location('pathname').should('eq', '/login');
  });

});