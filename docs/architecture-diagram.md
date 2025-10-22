# LinkLens AI - Architecture Diagram

## System Architecture Overview

```mermaid
graph TB
    %% User Interface Layer
    subgraph "Frontend Layer"
        UI[Web Dashboard<br/>Livewire + Flux Pro<br/>TailwindCSS 4.0]
        WS[WebSocket Client<br/>Real-time Updates]
    end

    %% Application Layer
    subgraph "Laravel Application Layer"
        subgraph "Controllers"
            LC[LinkedinController<br/>OAuth Handler]
            AC[API Controllers<br/>REST Endpoints]
        end
        
        subgraph "Livewire Components"
            LD[LinkedinDashboard<br/>Main Interface]
            AD[AnalyticsDashboard<br/>Insights View]
            AH[AutomationHistory<br/>Activity Log]
        end
        
        subgraph "Services"
            BAS[BedrockAgentService<br/>AI Integration]
            AS[AnalyticsService<br/>Data Processing]
        end
        
        subgraph "Job Queue System"
            GAP[GenerateAutomatedPost<br/>Content Creation]
            PCR[ProcessConnectionRequests<br/>Smart Filtering]
            QW[Queue Worker<br/>Background Processing]
        end
        
        subgraph "Models & Database"
            USER[User Model]
            LP[LinkedinProfile Model]
            AP[AutomatedPost Model]
            CF[ConnectionFilter Model]
            UA[UserActivity Model]
            DB[(MySQL Database<br/>User Data & Analytics)]
        end
    end

    %% AWS Cloud Services
    subgraph "AWS Cloud Infrastructure"
        subgraph "AI/ML Services"
            BR[Amazon Bedrock<br/>Nova Lite LLM<br/>amazon.nova-lite-v1:0]
            BRA[Bedrock Agent<br/>Decision Making]
        end
        
        subgraph "Compute Services"
            LF1[Lambda Function<br/>Content Processor]
            LF2[Lambda Function<br/>Connection Analyzer]
        end
        
        subgraph "Storage & API"
            S3[Amazon S3<br/>Content Storage<br/>Analytics Data]
            AGW[API Gateway<br/>REST Endpoints<br/>External Integration]
        end
        
        subgraph "Monitoring"
            CW[CloudWatch<br/>Logs & Metrics]
        end
    end

    %% External Services
    subgraph "External APIs"
        LI[LinkedIn API v2<br/>OAuth 2.0<br/>Profile & Posts<br/>Connections]
        OAI[OpenAI API<br/>Backup AI Service<br/>(Optional)]
    end

    %% Real-time Infrastructure
    subgraph "Real-time Services"
        REV[Laravel Reverb<br/>WebSocket Server<br/>Broadcasting]
        REDIS[Redis<br/>Session & Cache<br/>(Optional)]
    end

    %% Data Flow Connections
    UI --> LD
    UI --> AD
    UI --> AH
    WS <--> REV
    
    LD --> LC
    LD --> BAS
    AD --> AS
    
    LC <--> LI
    BAS --> BR
    BAS --> LF1
    
    GAP --> BAS
    PCR --> BAS
    QW --> GAP
    QW --> PCR
    
    USER --> LP
    USER --> AP
    USER --> CF
    USER --> UA
    
    LP --> DB
    AP --> DB
    CF --> DB
    UA --> DB
    
    BAS --> AGW
    LF1 --> S3
    LF2 --> S3
    BR --> CW
    LF1 --> CW
    LF2 --> CW
    
    GAP --> LI
    PCR --> LI
    
    REV --> UI
    AS --> S3

    %% Styling
    classDef aws fill:#FF9900,stroke:#232F3E,stroke-width:2px,color:#fff
    classDef laravel fill:#FF2D20,stroke:#fff,stroke-width:2px,color:#fff
    classDef external fill:#4CAF50,stroke:#fff,stroke-width:2px,color:#fff
    classDef frontend fill:#06B6D4,stroke:#fff,stroke-width:2px,color:#fff
    classDef database fill:#336791,stroke:#fff,stroke-width:2px,color:#fff
    
    class BR,BRA,LF1,LF2,S3,AGW,CW aws
    class UI,WS,LD,AD,AH,LC,AC,BAS,AS,GAP,PCR,QW laravel
    class LI,OAI external
    class UI,WS frontend
    class DB,REDIS database
```

## Technology Stack Breakdown

### **Frontend Technologies**
- **Livewire 3.x** - Full-stack reactive components
- **Livewire Flux Pro** - Premium UI component library
- **TailwindCSS 4.0** - Utility-first CSS framework
- **Vite** - Modern build tool and asset bundling
- **JavaScript/Alpine.js** - Client-side interactivity

### **Backend Framework**
- **Laravel 12.0** - Modern PHP framework
- **PHP 8.2+** - Latest PHP version
- **Eloquent ORM** - Database abstraction layer
- **Laravel Fortify** - Authentication scaffolding
- **Laravel Reverb** - WebSocket server

### **AWS AI/ML Services**
- **Amazon Bedrock** - Managed AI service
- **Nova Lite LLM** - Content generation model
- **Bedrock Agent** - AI decision making
- **AWS Lambda** - Serverless computing
- **Amazon S3** - Object storage
- **API Gateway** - RESTful API management
- **CloudWatch** - Monitoring and logging

### **Database & Storage**
- **MySQL** - Primary relational database
- **Amazon S3** - Cloud object storage
- **Redis** - Caching and sessions (optional)
- **Queue System** - Database-driven job processing

### **External Integrations**
- **LinkedIn API v2** - Social media automation
- **OAuth 2.0** - Secure authentication
- **OpenAI API** - Backup AI service
- **Guzzle HTTP** - API client library

### **Development & Testing**
- **Pest PHP** - Modern testing framework
- **Laravel Pint** - Code formatting
- **Paratest** - Parallel test execution
- **Laravel Herd** - Local development environment

## Data Flow Architecture

### **1. User Interaction Flow**
```
User → Web Dashboard → Livewire Components → Laravel Controllers → Services
```

### **2. AI Content Generation Flow**
```
User Request → GenerateAutomatedPost Job → BedrockAgentService → Amazon Bedrock Nova → Generated Content → LinkedIn API → Published Post
```

### **3. Connection Processing Flow**
```
LinkedIn Connection Request → ProcessConnectionRequests Job → BedrockAgentService → AI Analysis → Accept/Reject Decision → LinkedIn API Response
```

### **4. Real-time Updates Flow**
```
Background Job → Event Broadcasting → Laravel Reverb → WebSocket → Frontend Update
```

### **5. Analytics Data Flow**
```
LinkedIn API → Analytics Service → Database Storage → S3 Backup → Dashboard Visualization
```

## Security & Performance Features

### **Security Layers**
- OAuth 2.0 token management
- CSRF protection
- SQL injection prevention
- Encrypted token storage
- Rate limiting and throttling
- Input validation and sanitization

### **Performance Optimizations**
- Queue-based background processing
- Real-time WebSocket updates
- Database query optimization
- CDN-ready asset compilation
- Caching strategies
- Parallel test execution

### **Scalability Features**
- Serverless Lambda functions
- Auto-scaling queue workers
- Cloud-native storage (S3)
- Microservices architecture
- Event-driven design patterns

This architecture demonstrates a modern, scalable, and secure AI-powered LinkedIn automation platform leveraging AWS cloud services for intelligent decision-making and content generation.