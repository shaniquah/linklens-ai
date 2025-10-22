const AWS = require('aws-sdk');
const bedrock = new AWS.BedrockRuntime({ region: 'us-east-1' });

exports.handler = async (event) => {
    try {
        const { postType, tone, theme, voice, diction } = event;
        
        const prompt = buildPrompt(postType, tone, theme, voice, diction);
        
        const params = {
            modelId: 'amazon.nova-lite-v1:0',
            contentType: 'application/json',
            accept: 'application/json',
            body: JSON.stringify({
                inputText: prompt,
                textGenerationConfig: {
                    maxTokenCount: 500,
                    temperature: 0.7,
                    topP: 0.9
                }
            })
        };
        
        const response = await bedrock.invokeModel(params).promise();
        const result = JSON.parse(response.body.toString());
        
        return {
            statusCode: 200,
            body: JSON.stringify({
                content: result.results[0].outputText,
                metadata: {
                    model: 'amazon.nova-lite-v1:0',
                    timestamp: new Date().toISOString(),
                    parameters: { postType, tone, theme, voice, diction }
                }
            })
        };
        
    } catch (error) {
        console.error('Error generating content:', error);
        
        return {
            statusCode: 500,
            body: JSON.stringify({
                error: 'Content generation failed',
                message: error.message
            })
        };
    }
};

function buildPrompt(postType, tone, theme, voice, diction) {
    return `Generate a ${postType} LinkedIn post with the following parameters:
- Tone: ${tone}
- Theme: ${theme}
- Voice: ${voice}
- Diction: ${diction}

Requirements:
- Professional and engaging content
- Include relevant hashtags
- Appropriate length for ${postType} post
- Maintain ${tone} tone throughout
- Focus on ${theme} topic

Generate only the post content without additional commentary.`;
}