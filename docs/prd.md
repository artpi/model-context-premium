# PRD: Model Context Premium

## 1. Product overview
### 1.1 Document title and version
   - PRD: Model Context Premium
   - Version: 1.0

### 1.2 Product summary
   - Model Context Premium is a WordPress plugin designed to integrate with WooCommerce sites, enabling a Model Context Protocol (MCP) server. This server allows AI clients, such as Cursor, to interact with premium content hosted on the WooCommerce site.
   - The core functionality includes facilitating a checkout flow initiated from the MCP client for users to purchase access to premium content, and providing access to this content, which is stored as a vectorized database (e.g., in an OpenAI vector store).
   - The plugin aims to provide a novel way for content creators, especially researchers, to monetize their raw or precompiled data by allowing AI models to access and utilize this information without releasing the entire dataset publicly.

## 2. Goals
### 2.1 Business goals
   - Enable new revenue streams for WooCommerce site owners by selling AI-accessible premium content.
   - Provide a secure and controlled way for researchers and content creators to monetize their proprietary data.
   - Increase adoption of the Model Context Protocol by encouraging use of the `wordpress-mcp` plugin, which serves as a readily available MCP server for WordPress and WooCommerce.
### 2.2 User goals
   - For Researchers/Content Creators:
     - Easily link their existing OpenAI vector stores to WooCommerce products for monetization.
     - Monetize research data or specialized knowledge effectively.
     - Control access to their content through WooCommerce.
   - For AI Users/End Customers:
     - Seamlessly access and utilize premium, high-quality data directly within their AI clients.
     - Discover and purchase relevant premium content through their AI tools.
     - Enhance AI model performance with specialized, vetted information.
   - For Site Administrators:
     - Simple setup and configuration of the MCP server.
     - Integration with existing WooCommerce products and user accounts.
     - Robust security for content and transactions.
### 2.3 Non-goals
   - Direct download or full exposure of the raw premium content to end-users (access is intermediated by the AI and RAG).
   - Providing a standalone AI client; the plugin focuses on the server-side integration.
   - Complex content creation or vectorization tools within the plugin itself for the initial versions (assumes content is created and vectorized in an external service like OpenAI, and the Vector Store ID is then linked to a product).
## 3. User personas
### 3.1 Key user types
   - Researchers / Content Providers
   - AI Users (e.g., users of AI-powered research tools, developers integrating with AI)
   - WooCommerce Site Administrators
### 3.2 Basic persona details
   - **Dr. Aris, The Researcher**: Aris has compiled extensive datasets from his research and wants to share its value with others in a controlled way, generating income to fund further studies, without simply publishing the raw data online.
   - **Alex, The AI Developer**: Alex is building an AI application that requires specialized knowledge in various fields. They need a way for their AI to access verified, premium data sources on demand.
   - **Sarah, The Site Administrator**: Sarah manages a WooCommerce store that features expert content and tools. She wants to offer a new, innovative product that allows AI to access this expert content, and can easily link existing OpenAI Vector Stores to her products.
### 3.3 Role-based access

    - **Administrator**: Full control over the plugin settings, including API key management for OpenAI, managing which WooCommerce products are considered "premium content" for MCP access by linking Vector Store IDs, viewing logs, and managing user access/application passwords.
    - **Researcher/Content Provider (WordPress User with appropriate role, e.g., Editor/Author)**: Manages their content within OpenAI (or other vectorization services). Links their Vector Store IDs to WooCommerce products they manage on the site to make them available via the MCP server.
    - **Authenticated AI User (via Application Password)**: Can connect their AI client to the MCP server. Can trigger RAG queries to access purchased content. Can be prompted to purchase content if not already owned.

## 4. Functional requirements
   - **MCP Server Implementation** (Priority: High)
     - Expose a Model Context Protocol compliant server endpoint on the WordPress/WooCommerce site.
     - Handle requests from MCP clients.
   - **WooCommerce Integration** (Priority: High)
     - Utilize WooCommerce for user account management.
     - Link premium content access to specific WooCommerce products.
     - Initiate WooCommerce checkout flow for content purchases via the MCP client.
   - **User Authentication** (Priority: High)
     - Allow users to generate and use WordPress Application Passwords for authenticating their AI clients with the MCP server.
   - **Premium Content Access & RAG** (Priority: High)
     - Interface with a vector database (e.g., OpenAI Vector Store, identified by an ID) where premium content is stored.
     - Provide a Retrieval Augmented Generation (RAG) endpoint that AI clients can query.
     - If the user has purchased the relevant WooCommerce product, allow the RAG endpoint to search the corresponding vectorized data (linked via Vector Store ID).
   - **Content Purchase Flow** (Priority: High)
     - If an AI client attempts to access content not yet purchased by the user, the RAG endpoint should return a message/link to initiate the purchase of the corresponding WooCommerce product.
     - Seamlessly redirect or guide the user through the WooCommerce checkout process for the required content.
   - **Content Management (Linking Vector Stores)** (Priority: Medium for linking, Low for in-plugin creation/upload)
     - Mechanism for administrators or designated users (researchers) to input an OpenAI Vector Store ID into a WooCommerce product's settings.
     - Clear association between a WooCommerce product and a single OpenAI Vector Store ID (1:1 relationship).
## 5. User experience
### 5.1. Entry points & first-time user flow
   - **For AI Users**:
     - Discovering the MCP server (e.g., through a directory, researcher's website).
     - Adding the MCP server URL to their AI client (e.g., Cursor).
     - Creating an account on the WooCommerce site if they don't have one.
     - Generating an Application Password from their WooCommerce user profile.
     - Configuring their AI client with the MCP server URL and their Application Password.
   - **For Researchers/Content Providers**:
     - Learning about the platform and its capabilities.
     - Creating an account (if they don't have one) or logging into the WooCommerce site.
     - Creating/managing their content and vectorizing it using external tools (e.g., OpenAI dashboard).
     - Navigating to a WooCommerce product edit screen.
     - Pasting their OpenAI Vector Store ID into a designated field for the product and saving.
   - **For Site Administrators**:
     - Installing and activating the Model Context Premium plugin.
     - Configuring plugin settings, such as OpenAI API keys, default vector store details, etc.
     - Managing WooCommerce products that represent premium content.
### 5.2. Core experience
   - **AI User Accessing Content**:
     - The AI user, through their AI client, poses a query that could benefit from the premium research data.
     - The AI client (via the MCP protocol) sends a request to the RAG endpoint of the WordPress plugin.
     - **If content is not purchased**: The plugin checks if the user (identified by the Application Password) has purchased the relevant WooCommerce product. If not, the RAG endpoint responds with a message indicating the content is premium and provides a link or mechanism to initiate the purchase through WooCommerce. The AI client presents this to the user.
       - User clicks the purchase link, is taken to the WooCommerce checkout page, and completes the purchase.
     - **If content is purchased**: The plugin, confirming ownership, queries the vectorized database (e.g., OpenAI Vector Store) with the AI's request.
       - Relevant information is retrieved and returned to the AI client, which then uses it to formulate a response for the user.
     - The AI user receives an informed response from their AI client, powered by the premium content.
### 5.3. Advanced features & edge cases
   - Handling multiple vector stores or datasets associated with different WooCommerce products.
   - API rate limiting for the RAG endpoint to prevent abuse.
   - Graceful error handling for OpenAI API issues or vector store unavailability.
   - Mechanism for updating vectorized content (currently managed externally by the content provider via OpenAI dashboard; future plugin updates might offer more integrated solutions).
   - Support for different types of content to be vectorized (managed externally for now).
   - OAuth for user authentication as an alternative to Application Passwords.
   - In-plugin tools for uploading content directly to OpenAI vector stores (beyond initial MVP).
### 5.4. UI/UX highlights
   - Seamless integration with AI clients: the user interaction should feel natural within their existing AI tool.
   - Clear and actionable purchase prompts when premium content is encountered.
   - Simple application password generation process for users.
   - Easy-to-use interface for researchers to upload and manage their content (within WordPress admin).
## 6. Narrative
Dr. Aris, a dedicated researcher, has spent years compiling unique datasets but struggles to share their value broadly without giving away the raw data for free. He wants to monetize his work to fund future research. He discovers "Model Context Premium," a WordPress plugin for his WooCommerce site. He easily uploads his research, which is vectorized and linked to a WooCommerce product he creates. Meanwhile, Alex, an AI developer, uses an AI client that needs access to specialized data like Aris's. Alex adds Aris's MCP server to their client. When Alex's AI queries information related to Aris's research, it first checks if Alex has purchased access. If not, the AI prompts Alex with a direct link to buy Aris's "Research Access" product on the WooCommerce site. After a quick purchase, Alex's AI can now seamlessly query Aris's vectorized data through the RAG endpoint, receiving precise information. Aris successfully monetizes his research, and Alex enhances their AI application with valuable, verified data, all without direct exposure of the raw dataset.
## 7. Success metrics
### 7.1. User-centric metrics
   - Number of active AI client connections to MCP servers using the plugin.
   - Number of successful premium content purchases initiated via AI clients.
   - Average rating/feedback from researchers on ease of content monetization.
   - Task completion rate for AI users (e.g., successfully retrieving data after purchase).
   - Reduction in time for AI users to access specialized information.
### 7.2. Business metrics
   - Total revenue generated from sales of premium content accessed via the plugin.
   - Number of researchers/content providers actively using the plugin to sell content.
   - Growth rate of WooCommerce sites adopting the plugin.
   - Average revenue per researcher/content provider.
### 7.3. Technical metrics
   - API uptime for the MCP server and RAG endpoint.
   - Average query response time for the RAG endpoint.
   - Success rate of content vectorization and indexing processes.
   - Error rates in the purchase and content access flows.
   - Security incident count (should be zero).
## 8. Technical considerations
### 8.1. Integration points
   - WooCommerce: For products, pricing, checkout, user accounts, and application passwords.
   - OpenAI API: Specifically for interacting with Vector Stores (or a similar vector database service if alternatives are considered).
   - Model Context Protocol: Adherence to the protocol for client-server communication.
   - WordPress Plugin API: For creating admin interfaces, handling hooks, and managing settings.
### 8.2. Data storage & privacy
   - Secure storage of OpenAI API keys and any other sensitive configuration details.
   - User authentication details (managed by WordPress Application Passwords).
   - Premium content itself: Source files might be temporarily stored on the server for processing/vectorization, requiring secure handling and clear policies on retention. Vectorized data will reside in the chosen vector store (e.g., OpenAI).
   - Compliance with data privacy regulations (e.g., GDPR) regarding user data and uploaded content.
### 8.3. Scalability & performance
   - Efficient querying of the vector database to handle concurrent requests from multiple AI clients.
   - Optimized communication with the WooCommerce backend for checking purchase status.
   - Caching strategies for frequently accessed data or purchase statuses (where appropriate).
   - The WordPress site's hosting environment will need to be robust enough to handle the additional load from MCP server requests.
### 8.4. Potential challenges
   - Security of the MCP endpoint and protection against unauthorized access or data scraping.
   - Keeping the OpenAI (or other vector store) integration up-to-date with API changes.
   - Ensuring a smooth user experience for the purchase flow initiated from an external AI client.
   - Educating researchers on preparing their content for effective vectorization.
   - Managing costs associated with vector storage and API calls to services like OpenAI.
## 9. Milestones & sequencing
### 9.1. Project estimate
   - (Estimates removed as per request)
### 9.2. Team size & composition
   - (Team sizing removed as per request)
### 9.3. Suggested phases
   - **Phase 1**: Core `wordpress-mcp` Plugin Setup & RAG Tool
     - Install and activate the `wordpress-mcp` plugin.
     - Expose a basic, unauthenticated Model Context Protocol endpoint.
     - Implement admin settings for OpenAI API credentials.
     - Allow an admin to specify an OpenAI Vector Store ID (e.g., in plugin settings or associated with a test product).
     - Introduce a RAG (Retrieval Augmented Generation) tool within the MCP endpoint that can query the specified OpenAI Vector Store.
     - Key deliverables: A functional MCP endpoint capable of performing RAG against a pre-configured OpenAI Vector Store, accessible by an MCP client.
   - **Phase 2**: Authenticated Endpoint Access
     - Implement authentication for the MCP endpoint.
     - Require users to have a "customer" role (or other configurable role).
     - Require users to generate a WordPress Application Password, specifically scoped for "mcp" access if possible, or a general-purpose one.
     - The MCP endpoint should validate the Application Password before allowing access to the RAG tool.
     - Key deliverables: Secure MCP endpoint accessible only to authenticated WooCommerce users with the correct role and a valid Application Password.
   - **Phase 3**: WooCommerce Product Integration & Purchase Flow
     - Modify the product edit screen in WooCommerce to include a field for the OpenAI Vector Store ID (1:1 mapping: one product links to one Vector Store ID).
     - The RAG tool within the MCP endpoint will now identify the Vector Store ID based on a parameter (e.g., product ID) passed by the client.
     - Implement logic to check if the authenticated user has purchased the specific WooCommerce product associated with the requested Vector Store ID.
     - If the product is not purchased, the endpoint returns a message/link to purchase it.
     - If purchased, the RAG operation proceeds using the Vector Store ID linked to that product.
     - Key deliverables: Fully integrated purchase flow where access to specific vectorized datasets (via their linked products) is gated by WooCommerce purchases.
   - **Phase 4 (Post-MVP)**: Advanced Features & Researcher Tools
     - Interface for researchers/content providers to more easily manage linked Vector Store IDs (potentially beyond simple product meta fields).
     - Analytics on content usage via the MCP endpoint.
     - OAuth as an alternative authentication mechanism.
     - Tools or integrations for more seamless content uploading and vectorization directly from WordPress to OpenAI (if feasible and desired).
## 10. User stories
### 10.1. Administrator installs and configures the plugin
   - **ID**: US-001
   - **Description**: As a Site Administrator, I want to install and configure the Model Context Premium plugin so that I can set up the MCP server and connect it to our OpenAI vector store.
   - **Acceptance criteria**:
     - The plugin can be installed and activated like any other WordPress plugin.
     - There is a settings page where I can input my OpenAI API key.
     - There is a settings page where I can configure default vector store parameters if necessary.
     - The plugin status (e.g., connected to OpenAI) is clearly indicated.
### 10.2. Administrator associates premium content with a WooCommerce product
   - **ID**: US-002
   - **Description**: As a Site Administrator, I want to associate an existing OpenAI Vector Store ID with a specific WooCommerce product so that this product's content can be made available as premium content via the MCP server.
   - **Acceptance criteria**:
     - In the WooCommerce product edit screen, there is a dedicated field to input an OpenAI Vector Store ID.
     - I can save a valid Vector Store ID to this field for a product.
     - Each product can be linked to only one Vector Store ID.
     - The plugin uses this linked Vector Store ID to fetch content for RAG queries related to this product.
### 10.3. AI User creates an application password
   - **ID**: US-003
   - **Description**: As an AI User, I want to create an Application Password on the WooCommerce site so that I can securely authenticate my AI client with the MCP server.
   - **Acceptance criteria**:
     - I can navigate to my user profile on the WooCommerce site.
     - There is a section for creating Application Passwords.
     - I can give the Application Password a name (e.g., "Cursor AI Client").
     - A unique, strong password is generated for me to copy.
     - The generated password is not stored in a way that I can retrieve it again from the UI (standard WordPress behavior).
### 10.4. AI User configures MCP client
   - **ID**: US-004
   - **Description**: As an AI User, I want to configure my MCP client (e.g., Cursor) with the MCP server URL and my Application Password so that my client can communicate with the server.
   - **Acceptance criteria**:
     - My AI client has fields to input an MCP server URL and credentials.
     - I can successfully save the MCP server URL (provided by the WooCommerce site owner) and my Application Password in my AI client.
     - My AI client indicates a successful connection or provides a clear error message if connection fails due to incorrect credentials or server unavailability.
### 10.5. AI client attempts to access premium content (not purchased)
   - **ID**: US-005
   - **Description**: As an AI User, when my AI client queries the MCP server for information from a premium dataset I haven't purchased, I want to be informed that it requires a purchase and be provided with a way to buy it.
   - **Acceptance criteria**:
     - The MCP server correctly identifies that my user account (via Application Password) has not purchased the relevant WooCommerce product (identified by product ID or similar, linked to the requested vector store).
     - The RAG endpoint returns a standardized response indicating that access is restricted pending purchase.
     - The response includes a direct link or clear instructions to purchase the required WooCommerce product.
     - My AI client displays this information clearly.
### 10.6. AI User purchases premium content via link from AI client
   - **ID**: US-006
   - **Description**: As an AI User, after being prompted by my AI client, I want to easily purchase the required premium content so that I can access it.
   - **Acceptance criteria**:
     - Clicking the purchase link/following instructions from the AI client takes me to the correct WooCommerce product page or checkout.
     - I can complete the purchase using the standard WooCommerce checkout process.
     - After successful purchase, my WooCommerce account reflects ownership of the product.
### 10.7. AI client accesses premium content (purchased)
   - **ID**: US-007
   - **Description**: As an AI User, once I have purchased the WooCommerce product linked to a specific premium dataset, I want my AI client to be able to query the MCP server and receive relevant information from that dataset's Vector Store.
   - **Acceptance criteria**:
     - The MCP server correctly identifies that my user account has purchased the relevant WooCommerce product.
     - The RAG endpoint uses the OpenAI Vector Store ID linked to the purchased WooCommerce product to query with the AI client's request.
     - Relevant snippets or information from the vectorized content are returned to the AI client.
     - My AI client uses this information to provide an enhanced response.
     - The access is logged (for admin/researcher analytics, if implemented).
### 10.8. Researcher (Content Provider) uploads content for monetization
   - **ID**: US-008
   - **Description**: As a Researcher, I want to upload my research documents or data to the platform and associate them with a WooCommerce product I control, so I can monetize access to this data via AI clients.
   - **Acceptance criteria**:
     - There's an interface (e.g., within the WordPress admin, possibly integrated with WooCommerce product editing) where I can upload my content files.
     - I can select or create a WooCommerce product that will represent access to this content.
     - I can set the price and description for this WooCommerce product.
     - The system triggers the vectorization of my uploaded content and links it to the specified product and the MCP server.
     - I receive confirmation once my content is processed and available for purchase/access.
### 10.9. Secure access and authentication for MCP server
   - **ID**: US-009
   - **Description**: As a Site Administrator, I want the MCP server to require secure authentication (initially via Application Passwords) for all requests to protect premium content and user data.
   - **Acceptance criteria**:
     - All MCP server endpoints that provide access to data or initiate actions require a valid Application Password from a user with the 'customer' role (or other admin-defined role).
     - Unauthenticated or improperly authenticated requests are rejected with an appropriate error code.
     - Communication with the MCP server should ideally be over HTTPS (handled by WordPress/hosting environment).
     - Application Passwords can be revoked by users or administrators.
