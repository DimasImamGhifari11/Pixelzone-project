describe('Fitur CRUD Produk', () => {

  let authToken = null; 

  beforeEach(() => {
   
    cy.fixture('loginCredentials.json').then((credentials) => {
      cy.visit('/login');
      cy.get('input[type="email"]').type(credentials.email);
      cy.get('input[type="password"]').type(credentials.password);
      cy.get('button[type="submit"]').click();
      cy.location('pathname').should('include', '/dashboard');
      cy.window().its('sessionStorage').invoke('getItem', 'token').then(token => {
          authToken = token;
      });
    });
  });

  it('TC-CRUD-01: Verifikasi sistem dapat menambahkan produk baru dengan data valid', () => {
    const productName = `Helldivers 2 - ${Date.now()}`;
    
    cy.intercept('GET', '**/api/categories').as('getCategories');
    cy.visit('/create-product');
    cy.wait('@getCategories');

    cy.get('input[type="text"]').first().type(productName);
    cy.get('textarea').type('Game sci-fi FPS');
    cy.get('input[type="number"]').type('100000');
    cy.get('input[type="url"]').type('https://helldivers.2.jpg'); 
    cy.get('select').find('option').first().then(firstOption => {
        cy.get('select').select(firstOption.val());
    });

    cy.get('button[type="submit"]').contains('Simpan').click();

    cy.location('pathname').should('eq', '/dashboard'); 
    cy.contains('.product-card', productName).should('be.visible');
  });

  it('TC-CRUD-02: Sistem menolak pembuatan produk jika harga tidak diisi', () => {
    cy.intercept('GET', '**/api/categories').as('getCategories');
    cy.visit('/create-product');
    cy.wait('@getCategories');
    cy.get('input[type="text"]').first().type('FC24');
    cy.get('textarea').type('Game sepak bola');
    cy.get('input[type="url"]').type('https://fc24.jpg');
    cy.get('select').find('option').first().then(firstOption => {
        cy.get('select').select(firstOption.val());
    });
    
    cy.get('button[type="submit"]').contains('Simpan').click();

    cy.get('input[type="number"]:invalid').should('have.length', 1);
    cy.location('pathname').should('eq', '/create-product');
  });

  context('Dengan Produk yang Sudah Ada', () => {
    let testProduct;

    beforeEach(() => {
      cy.request({
        method: 'POST',
        url: 'http://127.0.0.1:8000/api/products',
        headers: { Authorization: `Bearer ${authToken}` },
        body: {
          name: `Test Product - ${Date.now()}`,
          description: 'A product for testing purposes',
          price: 999,
          category_id: 1,
          image: 'https://example.com/image.png'
        }
      }).then(response => {
        testProduct = response.body.data;
        cy.visit('/products'); 
      });
    });

    it('TC-CRUD-03: Sistem menampilkan daftar produk', () => {
      cy.contains('.product-card', testProduct.name).should('be.visible');
    });

    it('TC-CRUD-04: Sistem dapat mengubah data produk dengan benar', () => {
      const dynamicUpdatedProductName = `Updated Product ${Date.now()}`; 
      
      cy.intercept('GET', `**/api/products/${testProduct.id}`).as('getProductDetails');
      cy.visit('/products');
      cy.contains('.product-card', testProduct.name).within(() => {
        cy.contains('button', 'Edit').click();
      });
      cy.wait('@getProductDetails');

      cy.location('pathname').should('include', `/edit-product/${testProduct.id}`);
      cy.get('#name').clear().type(dynamicUpdatedProductName);
      cy.get('#price').clear().type('120000');
      
      
      cy.intercept('PUT', `**/api/products/${testProduct.id}`).as('updateProduct'); 
      cy.intercept('GET', '**/api/products').as('getUpdatedProducts');
      cy.get('button[type="submit"]').click(); 
      
      cy.wait('@updateProduct'); 
      cy.wait('@getUpdatedProducts'); 
      cy.wait(2500); 

      cy.location('pathname').should('eq', '/products');
      cy.contains('.product-card', dynamicUpdatedProductName).should('be.visible');
      cy.contains('.product-card', testProduct.name).should('not.exist');
    });

    it('TC-CRUD-05: Sistem menolak update jika harga bernilai negatif', () => {
      cy.intercept('GET', `**/api/products/${testProduct.id}`).as('getProductDetails');
      cy.visit('/products');
      cy.contains('.product-card', testProduct.name).within(() => {
        cy.contains('button', 'Edit').click();
      });
      cy.wait('@getProductDetails'); 

      cy.location('pathname').should('include', `/edit-product/${testProduct.id}`);
      cy.get('#price').clear().type('-5000').focus().trigger('blur'); 
      cy.wait(2500);
      
      cy.get('.text-danger').should('exist').and('be.visible').and('have.text', 'Harga tidak boleh kurang dari 1.');
      
      cy.location('pathname').should('include', `/edit-product/${testProduct.id}`);
    });

    it('TC-CRUD-06: Sistem dapat menghapus produk', () => {
      cy.on('window:confirm', () => true);
      cy.contains('.product-card', testProduct.name).within(() => {
        cy.contains('button', 'Hapus').click();
      });

      cy.contains(testProduct.name).should('not.exist');
    });
  });
});
