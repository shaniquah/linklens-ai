# AWS Deployment Guide for LinkLens AI Agent

## Prerequisites

1. AWS CLI installed and configured
2. AWS account with appropriate permissions
3. Composer installed for PHP dependencies

## Deployment Steps

### 1. Install AWS SDK
```bash
composer require aws/aws-sdk-php
```

### 2. Deploy AWS Infrastructure
```bash
# Deploy CloudFormation stack
aws cloudformation create-stack \
  --stack-name linklens-ai-infrastructure \
  --template-body file://aws/cloudformation-template.yaml \
  --parameters ParameterKey=ProjectName,ParameterValue=linklens-ai \
               ParameterKey=Environment,ParameterValue=production \
  --capabilities CAPABILITY_NAMED_IAM
```

### 3. Configure Environment Variables
Add to your `.env` file:
```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BEDROCK_REGION=us-east-1
AWS_S3_BUCKET=linklens-ai-content-storage-production
```

### 4. Enable Bedrock Models
```bash
# Enable Nova Lite model in Bedrock console or via CLI
aws bedrock put-model-invocation-logging-configuration \
  --logging-config cloudWatchConfig='{logGroupName="/aws/bedrock/modelinvocations",roleArn="arn:aws:iam::ACCOUNT:role/service-role/AmazonBedrockExecutionRoleForModelInvocation"}'
```

### 5. Test Integration
```bash
# Run Laravel queue worker to process jobs
php artisan queue:work --tries=3
```

## AWS Services Used

### ✅ Required Services
- **Amazon Bedrock** - Nova Lite LLM for content generation
- **AWS Lambda** - Serverless content processing functions
- **Amazon S3** - Content and analytics data storage
- **Amazon API Gateway** - RESTful API endpoints

### ✅ Agent Qualifications Met
- **LLM Integration**: Amazon Nova Lite via Bedrock
- **Reasoning Capabilities**: AI-powered decision making for connections and content
- **Autonomous Operation**: Automated posting and connection management
- **External Integrations**: LinkedIn API, database, web services
- **AWS Infrastructure**: Lambda, S3, API Gateway, Bedrock

## Architecture Overview

```
Laravel Application
├── BedrockAgentService (PHP)
├── GenerateAutomatedPost (Job)
├── ProcessConnectionRequests (Job)
└── AWS Integration
    ├── Bedrock Runtime (Nova Lite)
    ├── Lambda Functions
    ├── S3 Storage
    └── API Gateway
```

## Monitoring & Logging

- CloudWatch logs for Lambda functions
- Laravel logs for application events
- Bedrock model invocation logging
- S3 access logging

## Cost Optimization

- Use Nova Lite for cost-effective LLM operations
- Implement request caching
- Set up CloudWatch alarms for usage monitoring
- Use S3 lifecycle policies for data archival